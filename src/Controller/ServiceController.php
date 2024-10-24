<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ServiceController extends AbstractController
{
    #[Route('/service', name: 'app_service')]
    public function index(): Response
    {
        return $this->render('service/index.html.twig', [
            'controller_name' => 'ServiceController',
        ]);
    }
// fonction pour tester qu'on peut passer un variable nom et le changer dans l url
    #[Route('/service/{name}', name: 'app_service')]
    public function show($name): Response
    {
        return $this->render('service/show.html.twig', [
            "nom" => $name,
        ]);
    }
}
