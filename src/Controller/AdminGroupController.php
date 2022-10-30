<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Form\GroupType;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\TextUI\XmlConfiguration\Group;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminGroupController extends AbstractController
{

    private $repository;
    private $manager;

    public function __construct(GroupeRepository $repository, EntityManagerInterface $manager)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        
        
    }

    #[Route('/admin/groups', name: 'admin.group.index')]
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
        $groupe = new Groupe();

        $allGroups = $this->repository->findAll();

        $form =  $this->createForm(GroupType::class, $groupe);  
        $form->handleRequest($request) ;

        if ($form->isSubmitted() && $form->isValid()) { 

            $manager->persist($groupe);
            $manager->flush();

            return $this->redirectToRoute('admin.group.index');

        }

      


        return $this->render('admin/group/group.html.twig', [
            'controller_name' => 'AdminGroupController',
            'form' => $form->createView(),
            'groups' => $allGroups,
        ]);
    }
}
