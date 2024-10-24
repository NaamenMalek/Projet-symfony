<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    //    /**
    //     * @return Book[] Returns an array of Book objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('b.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Book
    //    {
    //        return $this->createQueryBuilder('b')
    //            ->andWhere('b.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    ///// DQL 1

public function countBooksByCategory(string $category): int
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
        'SELECT COUNT(b.ref)
         FROM App\Entity\Book b
         WHERE b.category = :category'
    )->setParameter('category', $category);

    return (int) $query->getSingleScalarResult();
}


/// DQL 2
public function findBooksBetweenDates(\DateTime $startDate, \DateTime $endDate): array
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
        'SELECT b
         FROM App\Entity\Book b
         WHERE b.publicationDate BETWEEN :startDate AND :endDate'
    )->setParameters([
        'startDate' => $startDate,
        'endDate' => $endDate,
    ]);

    return $query->getResult();

}
//// QB 2
/*DQL VERSION
public function searchBookByRef(string $ref): ?Book
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(
            'SELECT b
             FROM App\Entity\Book b
             WHERE b.ref = :ref'
        )->setParameter('ref', $ref);

        return $query->getOneOrNullResult();
    }*/

    public function searchBookByRef(string $ref): ?Book
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder(); // Vous devez initialiser $qb ici
    
        $qb->select('b')
           ->from('App\Entity\Book', 'b')
           ->where('b.ref = :ref') // Utiliser des guillemets pour 'b.ref'
           ->setParameter('ref', $ref);
    
        return $qb->getQuery()->getOneOrNullResult(); // Utilisation de getOneOrNullResult pour retourner un seul résultat ou null
    }



///QB 3
/* DQL
public function booksListByAuthors(): array
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
        'SELECT b
         FROM App\Entity\Book b
         JOIN b.author a
         ORDER BY a.username ASC'
    );

    return $query->getResult();
}  */

public function booksListByAuthors(): array
{
    $entityManager = $this->getEntityManager();
    $qb = $entityManager->createQueryBuilder();

    $qb->select('b')
       ->from('App\Entity\Book', 'b')
       ->join('b.author', 'a')
       ->orderBy('a.username', 'ASC');

    return $qb->getQuery()->getResult();
}



//// QB 4
/*DQL
public function findBooksBefore2023WithAuthorHavingMoreThan10Books(): array
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
        'SELECT b
         FROM App\Entity\Book b
         JOIN b.author a
         WHERE b.publicationDate < :year
         AND a.nb_books > :nbBooks'
    )->setParameters([
        'year' => new \DateTime('2023-01-01'),
        'nbBooks' => 10,
    ]);

    return $query->getResult();
}
    */
    public function findBooksBefore2023WithAuthorHavingMoreThan10Books(): array
    {
        $entityManager = $this->getEntityManager();
        $qb = $entityManager->createQueryBuilder();
    
        $qb->select('b')
           ->from('App\Entity\Book', 'b')
           ->join('b.author', 'a')
           ->where('b.publicationDate < :year')
           ->andWhere('a.nbBooks > :nbBooks') // Correction de la casse pour 'NbBooks'
           ->setParameter('nbBooks',10)
           ->setParameter('year',new \DateTime('2023-01-01'));
        
    
        return $qb->getQuery()->getResult();
    }
    



//// QB 5
/*DQL
public function updateCategoryToRomance(): void
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery(
        'UPDATE App\Entity\Book b
         SET b.category = :newCategory
         WHERE b.category = :oldCategory'
    )->setParameters([
        'newCategory' => 'Romance',
        'oldCategory' => 'Science-Fiction'
    ]);

    $query->execute();
}*/
public function updateCategoryToRomance(): void
{
    $entityManager = $this->getEntityManager();
    $qb = $entityManager->createQueryBuilder();

    $qb->update('App\Entity\Book', 'b')
       ->set('b.category', ':newCategory')
       ->where('b.category = :oldCategory')
       ->setParameter('newCategory', 'Romance')
       ->setParameter('oldCategory', 'Science-Fiction');

    $qb->getQuery()->execute();
}






}



