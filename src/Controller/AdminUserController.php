<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Role;

use App\Form\UserRegistrationType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class AdminUserController extends AbstractController
{

    private $repository;
    private $manager;

    public function __construct(UserRepository  $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        
        
    }

    #[Route('/admin/users', name: 'admin.users.index')]
    public function index(): Response
    {
        $users = $this->repository->findAll();

     

        return $this->render('admin/users/users.html.twig', [
            'controller_name' => 'AdminUserController',
            'users' => $users,
        ]); 
    }


    #[Route('/admin/users/new', name: 'admin.users.new')]
    public function new(Request $request, EntityManagerInterface $manager,  UserPasswordEncoderInterface $encoder): Response
    {

        $user = new User();



        $form =  $this->createForm(UserRegistrationType::class, $user);  
        $form->handleRequest($request) ;

        if ($form->isSubmitted() && $form->isValid()) { 
            $user->setLogin($user->getEmail());
            $user->setPassword("momo");

            $hash = $encoder->encodePassword($user,$user->getPassword());
            $user->setPassword($hash);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute('admin.users.index');

        }


        return $this->render('admin/users/new.html.twig', [
            'controller_name' => 'AdminUserController',
            'form' => $form->createView(),
        ]);
    }
}
