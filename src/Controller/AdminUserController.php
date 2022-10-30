<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    #[Route('/admin/users', name: 'admin.users.index')]
    public function index(): Response
    {
        return $this->render('admin/users/users.html.twig', [
            'controller_name' => 'AdminUserController',
        ]);
    }
}
