<?php

namespace App\Controller;

use App\Entity\Groupe;
use App\Entity\User;

use App\Form\GroupType;
use App\Repository\GroupeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\TextUI\XmlConfiguration\Group;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use App\Service\SendEmailService;


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


    private  function getAvailableGroupAdmin($users) {

        $admins = [];
        foreach ($users as $user) {

            if($user->getManagedGroup()== null && in_array("ROLE_ADMIN", $user->getRoles())){
                $admins[] = $user;
            }
           
        }
        return $admins;

    }

    private function getChosenAdmin ($users, $email) {
        foreach ($users as $user) {

            if($user->getEmail() == $email){
               return $user;
            }
           
        }
        return false;

    }


    #[Route('/admin/groups', name: 'admin.group.index')]
    public function index(Request $request, EntityManagerInterface $manager): Response
    {

        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $user = $this->get('security.token_storage')->getToken()->getUser();
       
        if(!$user->isIsTemporaryPasswordChange()){
            return $this->redirectToRoute('security.reset-temporary-password');
        }

        $allGroups = $this->repository->findAll();

        $users =    $this->manager->getRepository(User::class)->findAll();


        $groupe = new Groupe();

        $form =  $this->createForm(GroupType::class, $groupe);  

        return $this->render('admin/group/group.html.twig', [
            'controller_name' => 'AdminGroupController',
            'form' => $form->createView(),
            'groups' => $allGroups,
            'admins' => $this->getAvailableGroupAdmin($users)
        ]);
    }


    #[Route('/admin/groups/verify', name: 'admin.group.verify')]
    public function verify(Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->get('security.token_storage')->getToken()->getUser();

        if(!$user->isIsTemporaryPasswordChange()){
            return $this->redirectToRoute('security.reset-temporary-password');
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $isCorrectPassword = $this->passwordEncoder->isPasswordValid($user, $data['password']);

        return $this->json(['code' => 200, 'isCorrectPassword' => $isCorrectPassword], 200);
    }


    #[Route('/admin/groups/getGroupKey', name: 'admin.group.key')]
    public function getGroupKey(Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = $request->getContent();
        $data = json_decode($data, true);

        return $this->json(['code' => 200, 'key' => $this->repository->find($data['groupId'])->getPrivateKey()], 200);
    }

    #[Route('/admin/groups/save', name: 'admin.group.save')]
    public function save(Request $request, EntityManagerInterface $manager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');

        $user = $this->get('security.token_storage')->getToken()->getUser();

        $groupe = new Groupe();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $groupe->setPrivateKey($data['groupKey']);
        $groupe->setTitle($data['title']);

        $manager->persist($groupe);
        $manager->flush();

        return $this->json(['code' => 200, 'success' => TRUE], 200);
    }

    #[Route('/admin/groups/delete', name: 'admin.groups.delete')]
    public function deleteGroupe(Request $request)
    {
        $id = $request->get('id');
        $em = $this->container->get('doctrine')->getManager();
    
        $groupe = new Groupe();
        $groupe = $em->getRepository(Groupe::class)->findOneBy(array('id' => $id));

        $em->remove($groupe);
        $em->flush();
      
        return $this->json(1);

    }

    #[Route('/admin/groups/edit/{id}', name: 'admin.groups.edit')]
    public function editGroup(Request $request, $id): Response
    {
        $em = $this->container->get('doctrine')->getManager();
        $groupe = new Groupe();
        $groupe = $em->getRepository(Groupe::class)->findOneBy(array('id' => $id));


        $form =  $this->createForm(GroupType::class, $groupe);  
        $form->handleRequest($request) ;

        if ($form->isSubmitted() && $form->isValid()) { 
            
            $em->flush();

            return $this->redirectToRoute('admin.group.index');
        }

        return $this->render('admin/group/editGroup.html.twig', [
            'controller_name' => 'AdminGroupController',
            'form' => $form->createView(),
        ]);
    }


    #[Route('/admin/groups/assignGroupAdmin', name: 'admin.group.assignGroupAdmin')]
    public function assignGroupAdmin(Request $request, EntityManagerInterface $manager,  SendEmailService $mail,  UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if(!$user->isIsTemporaryPasswordChange()){
            return $this->redirectToRoute('security.reset-temporary-password');
        }
        
        $this->denyAccessUnlessGranted('ROLE_SUPER_ADMIN');
        $data = $request->getContent();
        $data = json_decode($data, true);

        $groupe = $this->repository->find($data['groupId']);
        $users =    $this->manager->getRepository(User::class)->findAll();

        $groupe->setPrivateKey($data['newEncryptedKey']);

        $admin = $this->getChosenAdmin($this->getAvailableGroupAdmin($users), $data['email']);
        $admin->setManagedGroup($groupe);
       


        $groupe->setGroupAdmin($admin);

        $title = $groupe->getTitle();
        $firstname = $admin->getFirstname();
        $tempKey =  $data['tempKey'];

        $hash = $encoder->encodePassword($admin,$tempKey);
        $admin->setPassword($hash);

        $admin->setPrivateKey($data['newEncryptedKey']);

        $manager->flush($admin);


        // Envoi du mail
        $mail->send(
            'no-reply@passwd_manager.fr',
            $data['email'],
            'Administration de groupe',
            'assigneAdmin',
            compact('title', 'firstname', 'tempKey')
        );
   
        return $this->json(['code' => 200, 'success' => TRUE], 200);
    }



    







}


