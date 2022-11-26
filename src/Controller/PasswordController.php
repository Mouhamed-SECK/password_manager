<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Password;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\HttpFoundation\Request;




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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $password = new Password();
       

        $user = $this->get('security.token_storage')->getToken()->getUser();


        $data = $request->getContent();
        $data = json_decode($data, true);

        $password->setTitle($data['title']);
        $password->setDescription($data['description']);
        $password->setUrl($data['usedUrl']);
        $password->setEncryptedPassword($data['createdPassword']);
        $password->setRecuparationEmail($data['email']);
        $password->setUsedLogin($data['login']);
        $password->setGroupe($user->getManagedGroup());





        $manager->persist($password);
        $manager->flush();

        return $this->json(['code' => 200, 'success' => TRUE], 200);
    }

}
