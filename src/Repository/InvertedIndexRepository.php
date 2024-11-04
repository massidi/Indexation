<?php

namespace App\Repository;

use App\Entity\InvertedIndex;
use App\Entity\Word;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<InvertedIndex>
 */
class InvertedIndexRepository extends ServiceEntityRepository
{
//    private mixed $entityManager;

    public function __construct(ManagerRegistry $registry, private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct($registry, InvertedIndex::class);
    }

    //    /**
    //     * @return InvertedIndex[] Returns an array of InvertedIndex objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?InvertedIndex
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function searchInIndex(string $term): array
    {
        // Récupère le mot dans l'index
        $wordRepo = $this->entityManager->getRepository(Word::class);
        $word = $wordRepo->findOneBy(['term' => $term]);

        if (!$word) {
            // Si le terme n'est pas trouvé, retourne un tableau vide
            return [];
        }

        // Récupère les entrées de l'index inversé pour ce mot, avec leur fréquence
        $invertedIndexRepo = $this->entityManager->getRepository(InvertedIndex::class);
        // Retourne les résultats
        return $invertedIndexRepo->createQueryBuilder('i')
            ->where('i.word = :word')
            ->setParameter('word', $word)
            ->orderBy('i.wordCount', 'DESC') // Trie les résultats par fréquence décroissante
            ->getQuery()
            ->getResult();
    }

    public function getWordsByDocumentId(int $documentId): array
    {
        // Get the repository for the inverted index
        $invertedIndexRepo = $this->entityManager->getRepository(InvertedIndex::class);

        // Use the query builder to fetch all words related to the given document ID
        return $invertedIndexRepo->createQueryBuilder('i')
            ->join('i.word', 'w') // Join to the Word entity
            ->where('i.document = :documentId') // Filter by the document ID
            ->setParameter('documentId', $documentId)
            ->orderBy('i.wordCount', 'DESC') // Optional: Sort by frequency in descending order
            ->getQuery()
            ->getResult();
    }





}
