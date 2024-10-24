<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    //    /**
    //     * @return Author[] Returns an array of Author objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Author
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    //// DQL 3
    public function findAuthorsByBookCount(int $minBooks, int $maxBooks): array
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT a
             FROM App\Entity\Author a
             WHERE a.nbBooks BETWEEN :minBooks AND :maxBooks'
        )
            ->setParameters([
                'minBooks' => $minBooks,
                'maxBooks' => $maxBooks,
            ]);

        return $query->getResult();
    }



    /// DQL 4
    public function deleteAuthorsWithNoBooks(): int
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'DELETE FROM App\Entity\Author a
             WHERE a.nbBooks = 0'
        );

        return $query->execute();
    }


    // Question 1 Query Builder
    public function listAuthorByEmail(): array
    {
        $entityManager = $this->getEntityManager();

        $qb = $entityManager->createQueryBuilder();
        $qb->select('a')
            ->from('App\Entity\Author', 'a')
            ->orderBy('a.email', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
