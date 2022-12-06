<?php

namespace App\Controller;

use App\Entity\User;

use App\Form\UserRegistrationType;
use App\Form\ResetPasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use App\Repository\UserRepository;
use App\Service\SendEmailService;
use App\Service\JWTService;
use App\Service\PasswordGenerator;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\Session\Session;



class AdminUserController extends AbstractController
{

    private $repository;
    private $manager;

    public function __construct(UserRepository  $repository, EntityManagerInterface $manager, JWTService $jwt)
    {
        $this->repository = $repository;
        $this->manager = $manager; 
    }

    #[Route('/admin/users', name: 'admin.users.index')]
    public function index(): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if(!$user->isIsTemporaryPasswordChange()){
            return $this->redirectToRoute('security.reset-temporary-password');
        }

        $users = $this->repository->findAll();

        return $this->render('admin/users/users.html.twig', [
            'controller_name' => 'AdminUserController',
            'users' => $users,
        ]); 
    }


    


    #[Route('/admin/users/createGroupUser', name: 'admin.users.createGroupUser')]
    public function createGroupUser(Request $request, EntityManagerInterface $manager, UserRepository $usersRepository,SendEmailService $mail, JWTService $jwt): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $admin = $this->get('security.token_storage')->getToken()->getUser();
        if(!$admin->isIsTemporaryPasswordChange()){
            return $this->redirectToRoute('security.reset-temporary-password');
        }
        $data = $request->getContent();
        $data = json_decode($data, true);

        $user = new User();

        if ($usersRepository->findOneByEmail($data['email']) == null) { 
            $user->setLogin($data['email']);

            $user->setPrivateKey($data['privateKey']);
            $user->setEmail($data['email']);
            $user->setFirstname($data['firstname']);
            $user->setLastname($data['lastname']);
            $user->setGroupe($admin->getManagedGroup());
            $user->setPassword($data['tempPassword']);
            $user->setRoles(["ROLE_USER"]);

            $admin->getManagedGroup()->addUser($user);

            $manager->persist($user);
            $manager->flush();

            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // On crée le Payload
            $payload = [
                'user_id' => $user->getId()
            ];

            // On génère le token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $mail->send(
                'no-reply@myPassword.fr',
                $user->getEmail(),
                'Activation de votre compte sur MYPassword',
                'register',
                compact('user', 'token')
            );

            return $this->json(['code' => 200 , 'success' => TRUE], 200);
        } else {
            return $this->json(['code' => 200 , 'success' => FALSE], 200);

        }
    }





    #[Route('/admin/users/new', name: 'admin.users.new')]
    public function new(Request $request, EntityManagerInterface $manager, SendEmailService $mail, JWTService $jwt): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $user = $this->get('security.token_storage')->getToken()->getUser();
        if(!$user->isIsTemporaryPasswordChange()){
            return $this->redirectToRoute('security.reset-temporary-password');
        }

        $user = new User();


        $form =  $this->createForm(UserRegistrationType::class, $user);  
        $form->handleRequest($request) ;



        if ($form->isSubmitted() && $form->isValid()) { 
            $user->setLogin($user->getEmail());
            //
          
            $manager->persist($user);
            $manager->flush();


            $header = [
                'typ' => 'JWT',
                'alg' => 'HS256'
            ];

            // On crée le Payload
            $payload = [
                'user_id' => $user->getId()
            ];

            // On génère le token
            $token = $jwt->generate($header, $payload, $this->getParameter('app.jwtsecret'));

            $mail->send(
                'no-reply@myPassword.fr',
                $user->getEmail(),
                'Activation de votre compte sur MYPassword',
                'register',
                compact('user', 'token')
            );

            return $this->redirectToRoute('admin.users.index');

        }

        return $this->render('admin/users/new.html.twig', [
            'controller_name' => 'AdminUserController',
            'form' => $form->createView(),
        ]);
    }



    #[Route('/verif/{token}', name: 'verify_user')]
    public function verifyUser($token, JWTService $jwt, UserRepository $usersRepository, EntityManagerInterface $em, PasswordGenerator $passwordGenerator,  SendEmailService $mail, UserPasswordEncoderInterface $encoder): Response
    {
        //On vérifie si le token est valide, n'a pas expiré et n'a pas été modifié
        if($jwt->isValid($token) && !$jwt->isExpired($token) && $jwt->check($token, $this->getParameter('app.jwtsecret'))){
            // On récupère le payload
            $payload = $jwt->getPayload($token);

            // On récupère le user du token
            $user = $usersRepository->find($payload['user_id']);

            //On vérifie que l'utilisateur existe et n'a pas encore activé son compte
            if($user && !$user->isIsVerified()){
                $temporaryPassword = "";
                if ($user->getPassword()=="") {
                    $temporaryPassword = $passwordGenerator->generate();

                } else {
                    $temporaryPassword =$user->getPassword();
                    $hash = $encoder->encodePassword($user,$temporaryPassword);
                    $user->setPassword($hash);

                    $mail->send(
                        'no-reply@myPassword.fr',
                        $user->getEmail(),
                        'Votre mot de Pass temporaire',
                        'temp_password',
                        compact('user', 'temporaryPassword')
                    );
                }

                $user->setIsVerified(true);
                $em->flush($user);

              
                $this->addFlash('success', 'Utilisateur activé');
                return $this->redirectToRoute('home');
            }
        }
        // Ici un problème se pose dans le token
        $this->addFlash('danger', 'Le token est invalide ou a expiré');
        return $this->redirectToRoute('app_login');
    }


    #[Route('/oubli-pass', name:'forgotten_password')]
    public function forgottenPassword(
        Request $request,
        UserRepository $usersRepository,
        TokenGeneratorInterface $tokenGenerator,
        EntityManagerInterface $entityManager,
        SendEmailService $mail
    ): Response
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if(!$user->isIsTemporaryPasswordChange()){
            return $this->redirectToRoute('security.reset-temporary-password');
        }

        $form = $this->createForm(ResetPasswordRequestFormType::class);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            //On va chercher l'utilisateur par son email
            $user = $usersRepository->findOneByEmail($form->get('email')->getData());

            // On vérifie si on a un utilisateur
            if($user){
                // On génère un token de réinitialisation
                $token = $tokenGenerator->generateToken();
                $user->setResetToken($token);
                $entityManager->persist($user);
                $entityManager->flush();

                // On génère un lien de réinitialisation du mot de passe
                $url = $this->generateUrl('reset_pass', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);
                
                // On crée les données du mail
                $context = compact('url', 'user');

                // Envoi du mail
                $mail->send(
                    'no-reply@passwd_manager.fr',
                    $user->getEmail(),
                    'Réinitialisation de mot de passe',
                    'password_reset',
                    $context
                );

                $this->addFlash('success', 'Email envoyé avec succès');
                return $this->redirectToRoute('app_login');
            }
            // $user est null
            $this->addFlash('danger', 'Un problème est survenu');
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/reset_password_request.html.twig', [
            'controller_name'=>'login',
            'requestPassForm' => $form->createView()
        ]);
    }

    #[Route('/oubli-pass/{token}', name:'reset_pass')]
    public function resetPass(
        string $token,
        Request $request,
        UserRepository $usersRepository,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response
    {
        // On vérifie si on a ce token dans la base
        $user = $usersRepository->findOneByResetToken($token);
        
        if($user){
            $form = $this->createForm(ResetPasswordFormType::class);

            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                // On efface le token
                $user->setResetToken('');
                $user->setPassword(
                    $passwordHasher->hashPassword(
                        $user,
                        $form->get('password')->getData()
                    )
                );
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', 'Mot de passe changé avec succès');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/reset_password.html.twig', [
                'controller_name'=>'login',
                'passForm' => $form->createView()
            ]);
        }
        $this->addFlash('danger', 'Jeton invalide');
        return $this->redirectToRoute('app_login');
    }


    #[Route('/users/change-temporary-password', name: 'reset_temporary_password')]
    public function home(): Response
    {
        return $this->render('/security/reset_temporary_password.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }


    #[Route('/admin/users/delete', name: 'admin.users.delete')]
    public function deleteUser(Request $request)
    {   
        $id = $request->get('id');
        
        $em = $this->container->get('doctrine')->getManager();
    
        $user = new User();
        $user = $em->getRepository(User::class)->findOneBy(array('id' => $id));

        $em->remove($user);
        $em->flush();

        return $this->json(1);

    }

#[Route('/admin/users/edit/{id}', name: 'admin.users.edit')]
    public function editUser(Request $request, $id): Response
    {
        $em = $this->container->get('doctrine')->getManager();
        $user = new User();
        $user = $em->getRepository(User::class)->findOneBy(array('id' => $id));


        $form =  $this->createForm(UserRegistrationType::class, $user);  
        $form->handleRequest($request) ;

        if ($form->isSubmitted() && $form->isValid()) { 
            
            $em->flush();

            return $this->redirectToRoute('admin.users.index');
        }

        return $this->render('admin/users/editUser.html.twig', [
            'controller_name' => 'AdminUserController',
            'form' => $form->createView(),
        ]);
    }
    
}