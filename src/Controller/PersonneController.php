<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\PersonneType;
use App\Repository\PersonneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PersonneController extends AbstractController
{
    #[Route('/personne', name: 'app_personne')]
    public function index(): Response
    {
        return $this->render('personne/index.html.twig', [
            'controller_name' => 'PersonneController',
        ]);
    }

    #[Route('/personneList', name: 'personne_list')]
    public function listAuthors(PersonneRepository $personneRepository): Response
    {
       
        $personnes = $personneRepository->findAll();
    
        return $this->render('personne/list.html.twig', [
            'personnes' => $personnes,
        ]);
    }


    #[Route('/personne/add/', name: 'add_personne')]
    public function addPersonne(Request $request, EntityManagerInterface $entityManager): Response
    {
        $personne = new Personne(); 
        $form = $this->createForm(PersonneType::class, $personne); 
    
        $form->handleRequest($request); 
    
        if ($form->isSubmitted() && $form->isValid()) {
          
            $entityManager->persist($personne);
    
        
            $entityManager->flush(); 
    
            return $this->redirectToRoute('personne_list');
        }
    
        return $this->render('personne/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    

    #[Route('/personne/edit/{cin}', name: 'edit_personne')]
public function editPersonne(Request $request, EntityManagerInterface $entityManager, string $cin): Response
{
    // Recherche de l'entité Personne par son CIN
    $personne = $entityManager->getRepository(Personne::class)->find($cin);
    
    // Vérification si la personne existe
    if (!$personne) {
        throw $this->createNotFoundException('Personne non trouvée');
    }

    // Création du formulaire
    $form = $this->createForm(PersonneType::class, $personne);
    $form->handleRequest($request);

    // Vérification du formulaire et validation
    if ($form->isSubmitted() && $form->isValid()) {
        // Logique métier supplémentaire si nécessaire

        // Sauvegarde des modifications dans la base de données
        $entityManager->flush();

        // Redirection après modification
        return $this->redirectToRoute('personne_list');
    }

    // Rendu du formulaire dans la vue
    return $this->render('Personne/edit.html.twig', [
        'form' => $form->createView(),
        'personne' => $personne,
    ]);
}

#[Route('/personne/delete/{cin}', name: 'delete_personne')]
public function deletePersonne(EntityManagerInterface $entityManager, int $cin): Response
{
    $personne = $entityManager->getRepository(Personne::class)->find($cin);
    if (!$personne) {
        throw $this->createNotFoundException('personne non trouvé');
    }

    $entityManager->remove($personne);
    $entityManager->flush();
    return $this->redirectToRoute('personne_list');
}




}
