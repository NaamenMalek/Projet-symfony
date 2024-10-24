<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController'
        ]);
    }
    
/*     #[Route('/authors/{name}', name: 'author2')]
    public function showAuthor($name): Response
    {
        return $this->render('author/show.html.twig', [
            'malek' => $name,
        ]);
    } */


    #[Route('/list', name: 'author3')]
    public function listAuthor(): Response
    {
        $authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' => 'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>  ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' => 'taha.hussein@gmail.com', 'nb_books' => 300),
            );
            return $this->render('author/list.html.twig', [
                'authors' => $authors,
            ]); 
    } 


    //la fonction qui permet de lire les auteurs de la base de données
#[Route('/authorsList', name: 'author_list')]
public function listAuthors(AuthorRepository $authorRepository): Response
{
    // Récupérer tous les auteurs depuis le repository
    $authors = $authorRepository->findAll();

    // Retourner la vue avec les auteurs
    return $this->render('author/list2.html.twig', [
        'authors' => $authors,
    ]);
}

//////////////////// Partie 1: Question 5

/*
Ajout d'un nouveau Author à travers le controller SANS Formulaire
*/
#[Route('/add-author', name: 'add_author')]
public function addAuthor(EntityManagerInterface $entityManager): Response
{
    // Créer une nouvelle instance de Author
    $author = new Author;
    $author->setUsername('Test1'); // Donnée statique
    $author->setEmail('Test1@example.com'); // Donnée statique


    // Persist (sauvegarder) l'auteur dans la base de données
    $entityManager->persist($author);
    $entityManager->flush(); // Envoie les changements à la base de données

    // Optionnel : retourner une réponse pour confirmer l'ajout
    return new Response('Auteur ajouté : ' . $author->getUsername());
}




//////////////////// Partie 1: Question 7
/*
Ajout d'un Auteur à travers le formulaire
*/

#[Route('/add-author2', name: 'add_author2')]
    public function addAuthor2(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Créer une nouvelle instance de Author
        $author = new Author();

        // Créer le formulaire
        $form = $this->createForm(AuthorType::class, $author);

        // Traitement de la soumission du formulaire
        $form->handleRequest($request);
     
        if ($form->isSubmitted() && $form->isValid()) {
            // Persist (sauvegarder) l'auteur dans la base de données
            $entityManager->persist($author);
            $entityManager->flush(); // Envoie les changements à la base de données

            // Redirige vers la liste des auteurs ou une autre page
            return $this->redirectToRoute('author_list');
        }

        // Retourner la vue du formulaire
        return $this->render('author/add.html.twig', [
            'form' => $form->createView(), // Crée la vue du formulaire
        ]);
    }


// Edit

    // src/Controller/AuthorController.php
#[Route('/edit-author/{id}', name: 'edit_author')]
public function editAuthor(Request $request, EntityManagerInterface $entityManager, int $id): Response
{
    // Récupérer l'auteur à modifier
    $author = $entityManager->getRepository(Author::class)->find($id);
    
    if (!$author) {
        throw $this->createNotFoundException('Auteur non trouvé');
    }

    // Créer le formulaire
    $form = $this->createForm(AuthorType::class, $author);

    // Traitement de la soumission du formulaire
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        // Mettre à jour l'auteur dans la base de données
        $entityManager->flush(); // Envoie les changements à la base de données

        return $this->redirectToRoute('author_list'); // Redirige vers la liste des auteurs
    }

    // Retourner la vue du formulaire pour modifier l'auteur
    return $this->render('author/edit.html.twig', [
        'form' => $form->createView(),
        'author' => $author,
    ]);
}


// delete

#[Route('/delete-author/{id}', name: 'delete_author')]
public function deleteAuthor(EntityManagerInterface $entityManager, int $id): Response
{
    // Récupérer l'auteur à supprimer
    $author = $entityManager->getRepository(Author::class)->find($id);
    
    if (!$author) {
        throw $this->createNotFoundException('Auteur non trouvé');
    }

    // Supprimer l'auteur
    $entityManager->remove($author);
    $entityManager->flush(); // Envoie les changements à la base de données

    return $this->redirectToRoute('author_list'); // Redirige vers la liste des auteurs
}
//// DQL 3
#[Route('/authors/search', name: 'authors_search')]
    public function searchAuthors(Request $request, AuthorRepository $authorRepository): Response
    {
        // Récupérer les valeurs minimales et maximales à partir de la requête
        $minBooks = $request->query->getInt('minBooks', 0);
        $maxBooks = $request->query->getInt('maxBooks', PHP_INT_MAX);

        // Récupérer les auteurs selon les critères
        $authors = $authorRepository->findAuthorsByBookCount($minBooks, $maxBooks);

        return $this->render('author/search_authors.html.twig', [
            'authors' => $authors,
        ]);
    }


    //// DQL 4

    #[Route('/authors/delete-zero', name: 'authors_delete_zero')]
    public function deleteAuthorsWithNoBooks(AuthorRepository $authorRepository): Response
    {
        // Supprimer les auteurs dont le nombre de livres est égal à zéro
        $deletedCount = $authorRepository->deleteAuthorsWithNoBooks();

        return $this->redirectToRoute('authors_search', [
            'message' => "$deletedCount authors have been deleted."
        ]);
        
    }
////// QueryBuilder Atelier QB question 1
#[Route('/QB1', name: 'QB1')]
public function list(AuthorRepository $authorRepository): Response
{
    // Récupérer la liste des auteurs triés par email via DQL
    $authors = $authorRepository->listAuthorByEmail();

    return $this->render('author/QB1.html.twig', [
        'authors' => $authors,
    ]);
}

}


