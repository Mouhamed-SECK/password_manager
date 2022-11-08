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
        $groupe = new Groupe();

        $allGroups = $this->repository->findAll();

        $form =  $this->createForm(GroupType::class, $groupe);  
        $form->handleRequest($request) ;

        if ($form->isSubmitted() && $form->isValid()) { 

            $uuid = Uuid::v1();
        
            $ciphering = "AES-128-CTR";
  
            // Use OpenSSl Encryption method
            $iv_length = openssl_cipher_iv_length($ciphering);
            $options = 0;
            
            // Non-NULL Initialization Vector for encryption
            $encryption_iv = '1234567891011121';
            
            // Store the encryption key
            $encryption_key = "GeeksforGeeks";
            $user = $this->get('security.token_storage')->getToken()->getUser();
            
           
            if ($this->passwordEncoder->isPasswordValid($user, $groupe->getPrivateKey())) {
                $groupe->setPrivateKey(openssl_encrypt($uuid, $ciphering, $groupe->getPrivateKey() , $options, $encryption_iv));
            } else {
                dd("Erreur pwd");
            }
         
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
