<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

use App\Form\UserRegistrationType;
use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Service\SendEmailService;
use App\Service\JWTService;
use App\Service\PasswordGenerator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
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

    #[Route('/user', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/users/verify', name: 'users.verify')]
    public function verify(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $isCorrectPassword = $this->passwordEncoder->isPasswordValid($user, $data['password']);

        return $this->json(['code' => 200, 'isCorrectPassword' => $isCorrectPassword], 200);
    }

    #[Route('/users/getPrivateKey', name: 'users.private-key')]
    public function getPrivateKey(Request $request, EntityManagerInterface $manager): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $request->getContent();
        $data = json_decode($data, true);

        return $this->json(['code' => 200, 'key' => $this->repository->find($data['userId'])->getPrivateKey()], 200);
    }

    #[Route('/users/changeUserTempPassword', name: 'user.change-user-temp-password')]
    public function changeUserTempPassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $data = $request->getContent();
        $data = json_decode($data, true);

        $user->setIsTemporaryPassword(true);
        $hash = $encoder->encodePassword($user,$data['password']);
        $user->setPassword($hash);
        $user->setPrivateKey($data['userPrivatekey']);
        $user->getManagedGroup()->setPrivateKey($data['userPrivatekey']);


        $manager->flush($user);

        return $this->json(['code' => 200, 'success' => TRUE], 200);
    }


    

}
