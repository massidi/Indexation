<?php

namespace App\Controller;

use App\Repository\DocumentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class NuageDeMotController extends AbstractController
{
    #[Route('/nuage/mot/{id}', name: 'app_nuage_de_mot',methods: ['GET'])]
    public function index($id,DocumentRepository $documentRepository): Response
    {
        $document= $documentRepository->find($id);

        $uploadDir = $this->getParameter('upload_directory').'/' . $document->getName(); // Adjust this if needed


        if (file_exists($uploadDir)) {
            $fileContent = file_get_contents($uploadDir);
        }else{
            return $this->json([
                'status' => 'error',
                'message' => "le fichier" .$document->getName(). " n'existe pas veillez verifier le chemin " ,
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->render('nuage_de_mot/index.html.twig', [
            'fileContent'  => $fileContent,
            'document'     => $document->getName(),
        ]);
    }
}
