<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Form\BookType;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }



// Afficher les Livres Publiés
#[Route('/books/published', name: 'published_books')]
public function publishedBooks(EntityManagerInterface $entityManager): Response
{
    // Récupérer tous les livres publiés
    $publishedBooks = $entityManager->getRepository(Book::class)->findBy(['published' => true]);

    // Récupérer tous les livres non publiés
    $unpublishedBooksCount = $entityManager->getRepository(Book::class)->count(['published' => false]);

    return $this->render('book/published.html.twig', [
        'publishedBooks' => $publishedBooks,
        'unpublishedBooksCount' => $unpublishedBooksCount,
    ]);
}



    // Ajout Book
    #[Route('/book/add/', name: 'app_book_controller_2')]
    public function addBook(Request $request, EntityManagerInterface $entityManager): Response
    {
        $book = new Book();
        $author = new Author();
        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrement de l'entité
        $entityManager->persist($book);
        $val=  $book->getAuthor()->getNbBooks();
        $book->getAuthor()->setNbBooks($val+1);
            $entityManager->flush();

            return $this->redirectToRoute('published_books'); // Redirige vers une route appropriée
        }

        return $this->render('book/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }



// Edit BOOK
    #[Route('/book/edit/{id}', name: 'edit_book')]
public function editBook(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    $book = $entityManager->getRepository(Book::class)->find($id);
    if (!$book) {
        throw $this->createNotFoundException('Livre non trouvé');
    }

    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
        $val=  $book->getAuthor()->getNbBooks();
        $book->getAuthor()->setNbBooks($val+1);
        $entityManager->flush();
        return $this->redirectToRoute('published_books');
    }

    return $this->render('book/edit.html.twig', [
        'form' => $form->createView(),
        'book' => $book,
    ]);
}


// delete
#[Route('/book/delete/{id}', name: 'delete_book')]
public function deleteBook(EntityManagerInterface $entityManager, int $id): Response
{
    $book = $entityManager->getRepository(Book::class)->find($id);
    if (!$book) {
        throw $this->createNotFoundException('Livre non trouvé');
    }

    $entityManager->remove($book);
    $entityManager->flush();
    return $this->redirectToRoute('published_books');
}


// detail
#[Route('/book/view/{id}', name: 'book_details')]
public function viewBook(EntityManagerInterface $entityManager, int $id): Response
{
    $book = $entityManager->getRepository(Book::class)->find($id);
    if (!$book) {
        throw $this->createNotFoundException('Livre non trouvé');
    }

    return $this->render('book/view.html.twig', [
        'book' => $book,
    ]);
}



///// DQL 1
#[Route('/books/count-romance', name: 'books_count_romance')]
public function countRomanceBooks(BookRepository $bookRepository): Response
{
    // Compter le nombre de livres dans la catégorie "Romance"
    $count = $bookRepository->countBooksByCategory('historique');

    return $this->render('book/count_romance.html.twig', [
        'count' => $count,
    ]);
}


//// DQL 2
#[Route('/books/published-between', name: 'books_published_between')]
    public function listBooksBetweenDates(BookRepository $bookRepository): Response
    {
        // Définir les dates de début et de fin
        $startDate = new \DateTime('2018-01-01');
        $endDate = new \DateTime('2024-12-31');

        // Récupérer les livres publiés entre ces dates
        $books = $bookRepository->findBooksBetweenDates($startDate, $endDate);

        return $this->render('book/list_books_between.html.twig', [
            'books' => $books,
        ]);
    }


//// QB2
#[Route('/books/search', name: 'book_search')]
    public function search(Request $request, BookRepository $bookRepository): Response
    {
        // Récupérer la valeur de la référence depuis la requête
        $ref = $request->query->get('ref', '');

        $book = null;
        if (!empty($ref)) {
            // Chercher le livre par référence si une référence est fournie
            $book = $bookRepository->searchBookByRef($ref);
        }

        return $this->render('book/search.html.twig', [
            'book' => $book,
            'ref' => $ref,
        ]);
    }



    //// QB2
    #[Route('/books', name: 'book_list')]
    public function listAndSearch(Request $request, BookRepository $bookRepository): Response
    {
        // Récupérer la valeur de la référence depuis la requête
        $ref = $request->query->get('ref', '');

        // Initialiser les variables
        $books = [];
        $searchedBook = null;

        if (!empty($ref)) {
            // Chercher le livre par référence si une référence est fournie
            $searchedBook = $bookRepository->searchBookByRef($ref);
        } else {
            // Si aucune référence n'est fournie, on affiche la liste complète des livres
            $books = $bookRepository->findAll();
        }

        return $this->render('book/list2.html.twig', [
            'books' => $books,
            'searchedBook' => $searchedBook,
            'ref' => $ref,
        ]);
    }


    //// QB3
    #[Route('/books/by-authors', name: 'books_by_authors')]
    public function booksByAuthors(BookRepository $bookRepository): Response
    {
        // Récupérer la liste des livres triée par auteur
        $books = $bookRepository->booksListByAuthors();

        return $this->render('book/books_by_authors.html.twig', [
            'books' => $books,
        ]);
    }



    //// QB 4
    #[Route('/books/old-and-prolific-authors', name: 'books_old_and_prolific')]
    public function booksBefore2023WithProlificAuthors(BookRepository $bookRepository): Response
    {
        // Récupérer la liste des livres publiés avant 2023 avec des auteurs ayant plus de 10 livres
        $books = $bookRepository->findBooksBefore2023WithAuthorHavingMoreThan10Books();

        return $this->render('book/old_and_prolific_authors.html.twig', [
            'books' => $books,
        ]);
    }



    /// QB 5

    #[Route('/books/update-category', name: 'books_update_category')]
    public function updateCategory(BookRepository $bookRepository): Response
    {
        // Mise à jour de la catégorie "Science-Fiction" à "Romance"
        $bookRepository->updateCategoryToRomance();

        // Ajout d'un message flash pour confirmer la mise à jour
        $this->addFlash('success', 'All books with category "Science-Fiction" have been updated to "Romance".');

        // Rediriger vers la liste des livres (ou une autre page de votre choix)
        return $this->redirectToRoute('book_list');
    }

}






