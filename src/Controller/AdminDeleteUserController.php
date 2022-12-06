<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminDeleteUserController extends AbstractController
{
    #[Route('/admin/delete/user', name: 'app_admin_delete_user')]
    public function index(): Response
    {
        return $this->render('admin_delete_user/index.html.twig', [
            'controller_name' => 'AdminDeleteUserController',
        ]);
    }
}
