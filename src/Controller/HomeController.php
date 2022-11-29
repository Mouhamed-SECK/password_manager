<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\Password;
use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends AbstractController
{
    private $repository;
    private $manager;
    private $passwordEncoder;

    public function __construct(UserRepository $repository, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/home', name: 'home')]
    public function home(): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if(!$user->isIsTemporaryPasswordChange()){
            return $this->redirectToRoute('security.reset-temporary-password');
        }
        $passwords = $this->manager->getRepository(Password::class)->findAll();
    

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'passwords'  =>  $passwords
        ]);
    }


    #[Route('/admin/home', name: 'Adminhome')]
    public function adminHome(): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if(!$user->isIsTemporaryPasswordChange()){
            return $this->redirectToRoute('security.reset-temporary-password');
        }


        $users = $this->repository->findGroupUsers($user->getManagedGroup()->getId());

    

        return $this->render('home/adminHome.html.twig', [
            'controller_name' => 'HomeController',
            'users'  =>  $users
        ]);
    }


    #[Route('/', name: 'index')]
    public function index(): Response
    {
        return $this->redirectToRoute('home');
    }

    #[Route('/reset-temporary-password', name: 'security.reset-temporary-password')]
    public function forcePasswordChange(Request $request, EntityManagerInterface $manager): Response
    {  
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if($user->isIsTemporaryPasswordChange()){
            return $this->redirectToRoute('home');
        }

        return $this->render('security/reset_temporary_password.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }




}
