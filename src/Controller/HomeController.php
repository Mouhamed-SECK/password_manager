<?php

namespace App\Controller;

use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'home')]
    public function home(): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if(!$user->isIsTemporaryPasswordChange()){
            //$this->forward('App\Controller\AdminUserController::forcePasswordChange');
            return $this->redirectToRoute('security.reset-temporary-password');
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute('home');
    }

    #[Route('/reset-temporary-password', name: 'security.reset-temporary-password')]
    public function forcePasswordChange(Request $request, UserRepository $usersRepository, TokenGeneratorInterface $tokenGenerator, EntityManagerInterface $manager): Response
    {   
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $form =  $this->createForm(UserRegistrationType::class, $user);  
        $form->handleRequest($request) ;

        if ($form->isSubmitted() && $form->isValid()) { 
            $user->setLogin($user->getEmail());
          
            $manager->persist($user);
            $manager->flush();


            return $this->redirectToRoute('admin.users.index');
        }

        return $this->render('security/reset_temporary_password.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
}
