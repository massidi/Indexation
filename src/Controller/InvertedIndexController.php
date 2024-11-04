<?php

namespace App\Controller;

use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\InvertedIndexService; // Fixed namespace for service
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Routing\Attribute\Route;

class InvertedIndexController extends AbstractController
{
    private InvertedIndexService $invertedIndexService; // Changed public to private

    public function __construct(InvertedIndexService $invertedIndexService)
    {
        $this->invertedIndexService = $invertedIndexService;
    }

    #[Route(path: '/search_page', name: 'search_page')] // Added name for the route
    public function search(Request $request): Response
    {

        $term = $request->query->get('term');

        $results = [];
        if ($term) {
            // Search for the term in the index
            $results = $this->invertedIndexService->searchInIndex($term);


            // Read the first 50 characters of each document
            foreach ($results as $result) {

//                dd($result->getDocument());

//                $document=


                $documentPath = $this->getParameter('upload_directory') . '/' . $result->getDocument()->getName();

                if (file_exists($documentPath)) {
                    $fileContent = file_get_contents($documentPath);
                    $preview = mb_substr($fileContent, 0, 100); // Get the first 50 characters
                    $result->preview = $preview; // Add the preview to the result object
                } else {
                    $result->preview = "Document non trouvé.";
                }
            }
        }

//        dd($results);


//        dd($results);

        // Render the search page with the search results
        return $this->render('inverted_index/search.html.twig', [
            'term' => $term,
            'results' => $results,
        ]);
    }

    #[Route('/upload', name: 'file_upload', methods: ['GET', 'POST'])]
    public function upload(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            // Get the uploaded file
            $files = $request->files->get('documents');

//            dd($files);



            foreach ($files as $file) {
                if ($file) {
                    // Vérifie si le fichier est bien un fichier .txt
                    if ($file->getClientOriginalExtension() !== 'txt') {
                        return $this->json([
                            'status' => 'error',
                            'message' => 'Seuls les fichiers .txt sont autorisés.',
                        ], Response::HTTP_BAD_REQUEST);
                    }

                    $uploadDir = $this->getParameter('upload_directory'); // public/document/

                    try {
                        $destinationPath = $uploadDir . '/' . $file->getClientOriginalName();

                        // Vérifie si le fichier existe déjà dans le répertoire cible
                        if (file_exists($destinationPath)) {
                            continue; // Passe au fichier suivant si le fichier existe déjà
                        }

                        // Déplace le fichier téléchargé vers le répertoire spécifié
                        $filePath = $file->move($uploadDir, $file->getClientOriginalName());

                        // Traite le fichier téléchargé avec le InvertedIndexService
                        $this->invertedIndexService->createInvertedIndex($filePath->getRealPath());

                    } catch (FileException $e) {
                        return $this->json([
                            'status' => 'error',
                            'message' => "Échec du téléchargement du fichier: " . $file->getClientOriginalName(),
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    } catch (\Exception $e) {
                        return $this->json([
                            'status' => 'error',
                            'message' => "Une erreur est survenue lors du traitement du fichier: " . $e->getMessage(),
                        ], Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                }
            }

// Réponse JSON finale une fois que tous les fichiers ont été traités avec succès

            return $this->redirectToRoute('search_page', [
                'status' => 'success',
                'message' => 'Tous les fichiers ont été traités avec succès.',
            ], 301);

        }

        // Affiche le formulaire d'upload

        return $this->render('inverted_index/upload.html.twig');

    }

    #[Route('/download/{filename}', name: 'download_file')]
    public function download(string $filename): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $uploadDir = $this->getParameter('upload_directory'); // Adjust this if needed
        $filePath = $uploadDir . '/' . $filename;

        if (!file_exists($filePath)) {
            throw $this->createNotFoundException('File not found');
        }

        return $this->file($filePath, null, ResponseHeaderBag::DISPOSITION_ATTACHMENT);
    }
}