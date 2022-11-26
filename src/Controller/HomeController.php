<?php

namespace App\Controller;

use App\Repository\UserRepository;

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
    public function forcePasswordChange(Request $request, EntityManagerInterface $manager): Response
    {  
        return $this->render('security/reset_temporary_password.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

}
