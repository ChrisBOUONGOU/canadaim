<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImmigrationController extends AbstractController
{
    #[Route('/immigration', name: 'immigration')]
    public function index(): Response
    {
        return $this->render('immigration/index.html.twig', [
            'controller_name' => 'ImmigrationController',
        ]);
    }
}
