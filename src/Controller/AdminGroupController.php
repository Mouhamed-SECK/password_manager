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
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

// Include Library Namespaces
use Symfony\Component\Uid\Uuid;


class AdminGroupController extends AbstractController
{

    private $repository;
    private $manager;
    private $passwordEncoder;

    public function __construct(GroupeRepository $repository, EntityManagerInterface $manager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->passwordEncoder = $passwordEncoder;
           
    }

    #[Route('/admin/groups', name: 'admin.group.index')]
    public function index(Request $request, EntityManagerInterface $manager): Response
    {
       
        $allGroups = $this->repository->findAll();

        $groupe = new Groupe();

        $form =  $this->createForm(GroupType::class, $groupe);  

        return $this->render('admin/group/group.html.twig', [
            'controller_name' => 'AdminGroupController',
            'form' => $form->createView(),
            'groups' => $allGroups,
        ]);
    }


    #[Route('/admin/groups/verify', name: 'admin.group.verify')]
    public function verify(Request $request, EntityManagerInterface $manager): Response
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();


        $data = $request->getContent();
        $data = json_decode($data, true);

        $isCorrectPassword = $this->passwordEncoder->isPasswordValid($user, $data['password']);

        return $this->json(['code' => 200, 'isCorrectPassword' => $isCorrectPassword], 200);
    }


    #[Route('/admin/groups/save', name: 'admin.group.save')]
    public function save(Request $request, EntityManagerInterface $manager): Response
    {

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $groupe = new Groupe();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $groupe->setPrivateKey($data['groupKey']);
        $groupe->setTitle($data['title']);
        $groupe->setGroupAdmin($user);

        $manager->persist($groupe);
        $manager->flush();

        return $this->json(['code' => 200, 'success' => TRUE], 200);
    }
}


