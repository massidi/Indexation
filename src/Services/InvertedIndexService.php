<?php

namespace App\Services;

use App\Entity\Word;
use App\Entity\Document;
use App\Entity\InvertedIndex;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use voku\helper\StopWords;
use voku\helper\StopWordsLanguageNotExists;

class InvertedIndexService
{
    private EntityManagerInterface $entityManager;

//    private Doc2Txt $doc2Txt;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @throws StopWordsLanguageNotExists
     */
    public function parcourirArborescenceEtIndexer($dossier)
    {
        // Vérification de l'existence du dossier
        if (!is_dir($dossier)) {
            throw new Exception("Le dossier spécifié n'existe pas : " . $dossier);
        }

        // Utilisation d'un itérateur pour parcourir tous les fichiers dans les sous-dossiers
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dossier));

        // Parcourir chaque fichier trouvé
        foreach ($it as $fichier) {
            // Vérification de l'extension du fichier
            if ($fichier->isFile() && $fichier->getExtension() === 'txt') {
                // Appel de la fonction d'indexation pour chaque fichier .txt
                $this->createInvertedIndex($fichier->getPathname());
            }
        }
    }

    /**
     * @throws StopWordsLanguageNotExists
     * @throws Exception
     */
    public function createInvertedIndex(string $filePath): void
    {
        // Check if the file exists
        if (!file_exists($filePath)) {
            throw new Exception('File not found: ' . $filePath);
        }

        // Get the file content
        $content = file_get_contents($filePath);

        // Preprocess and clean the text
        $words = $this->preprocessText($content);

//        dd($words);
        $wordFrequencies = array_count_values($words);

        // Store the document in the database
        $document = new Document();
        $document->setName(basename($filePath)); // Store the filename only

        $this->entityManager->persist($document);

//        dd($wordFrequencies);

        // For each word, store it in the database and create the inverted index
        foreach ($wordFrequencies as $wordText=>$count) {




            $word = new Word();
            $word->setTerm($wordText);
            $this->entityManager->persist($word);


            // Create the inverted index entry
            $invertedIndex = new InvertedIndex();
            $invertedIndex->setWord($word);
            $invertedIndex->setWordCount($count);

            $invertedIndex->setDocument($document);

            $this->entityManager->persist($invertedIndex);
        }

        // Flush all changes to the database
        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    public function searchInIndex(string $term): array
    {
        // Preprocess the search term
        $term = strtolower($term);
        $term = preg_replace('/[^\w\s]/', '', $term); // Remove punctuation

        // Initialize results and allInvertedIndexResults
        $results = [];
        $allInvertedIndexResults = []; // Initialize this variable

        if ($term) {
            // Search for all words that match the term
            $wordRepo = $this->entityManager->getRepository(Word::class);
            $words = $wordRepo->findBy(['term' => $term]);

            // If you want to handle the case where multiple words match the term
            if (!empty($words)) {
                foreach ($words as $word) {
                    // Here, you could gather results based on the word ID
                    $invertedIndexRepo = $this->entityManager->getRepository(InvertedIndex::class);
                    $invertedIndexResults = $invertedIndexRepo->createQueryBuilder('i')
                        ->select('i, d') // Select both inverted index and document
                        ->join('i.document', 'd') // Join with Document entity
                        ->where('i.word = :word')
                        ->setParameter('word', $word)
                        ->orderBy('i.wordCount', 'DESC') // Sort by frequency in descending order
                        ->getQuery()
                        ->getResult();

                    // Merge the results if needed
                    $allInvertedIndexResults = array_merge($allInvertedIndexResults, $invertedIndexResults);
                }
            }

            // Check if there are results before sorting
            if (!empty($allInvertedIndexResults)) {
                // Sort all results by word count in descending order
                usort($allInvertedIndexResults, function ($a, $b) {
                    return $b->getWordCount() <=> $a->getWordCount();
                });
            }

            // Store the sorted results
            $results = $allInvertedIndexResults;
        }

        return $results;
    }

    /**
     * @throws StopWordsLanguageNotExists
     */
    private function preprocessText(string $text): array
    {

        $stopWord = new StopWords();

        $stopWords = $stopWord->getStopWordsFromLanguage('fr');

        // Convert to lowercase
        $text = strtolower($text);

        // Remove punctuation
        $text = preg_replace('/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/', '', $text); // Supprimer les adresses e-mail
        $text = preg_replace('/[^\p{L}\s]/u', ' ', $text); // Remplacer les caractères non alphabétiques par des espaces
        $text = preg_replace('/\s+/', ' ', $text);         // Remplacer les espaces multiples par un seul espace
        $text = preg_replace('/\b\w{2}\b/u', '', $text);    // Supprimer les mots de deux lettres
        $text = trim($text); // Retirer les espaces en début et fin de chaîne


        // Tokenize into words
        $words = explode(' ', $text);


        // Remove stopwords
        // Filtrer les stopwords
        // Vérifier si le mot n'est pas dans les stopwords et si la longueur du mot est supérieure à 1

        return array_filter($words, function ($word) use ($stopWords) {
            return !in_array($word, $stopWords) && strlen($word) > 1;
        });
    }
}