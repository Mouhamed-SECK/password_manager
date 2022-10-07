<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PoliticsController extends AbstractController
{
    #[Route('/politics', name: 'app_politics')]
    public function index(): Response
    {
        return $this->render('politics/index.html.twig', [
            'controller_name' => 'PoliticsController',
        ]);
    }
}
