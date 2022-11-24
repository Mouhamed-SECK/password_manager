<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PasswordController extends AbstractController
{
    #[Route('/admin/password/create', name: 'admin.password.create')]
    public function index(): Response
    {
        return $this->render('password/index.html.twig', [
            'controller_name' => 'PasswordController',
        ]);
    }


    #[Route('/admin/password/save', name: 'admin.password.save')]
    public function create(Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $user = $this->get('security.token_storage')->getToken()->getUser();


        $data = $request->getContent();
        $data = json_decode($data, true);

        $manager->persist();
        $manager->flush();

        return $this->json(['code' => 200, 'success' => TRUE], 200);
    }

}
