<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\Mime\Email;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

   /**
     * @Route("/auth", name="authen")
    */
class AuthController extends AbstractController
{
   
    /**
     * @Route("/login", name="auth_login", methods={"GET"})
    */
    public function login(Request $request): Response
    {
        $error = "";
        return $this->render('security/signin.html.twig', [
            'error' => $error,
            'email' => '',
            'password' => '',

        ]);
    }

    /**
     * @Route("/login", name="login_post", methods={"POST"})
    */
    public function login_post(Request $request, JWTTokenManagerInterface $JWTManager, UserPasswordHasherInterface $encoder,UserRepository $userRepository): Response
    {
        $email = $request->get('email', '');
        $password = $request->get('password', '');
        $error = "";
        $errors = "";
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return $this->render('security/signin.html.twig', [
                'error' => "Email ou mot de passe incorrect",
                'email' => $email,
                'password' => $password,
            ]);
        }

        if (!$encoder->isPasswordValid($user, $password)) {
            return $this->render('security/signin.html.twig', [
                'error' => "Email ou mot de passe incorrect",
                'email' => $email,
                'password' => $password,
            ]);
        }

        $userData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'role' => $user->getRole(), 
        ];

        $token = $JWTManager->createFromPayload($user,$userData);

        $response = new RedirectResponse('/');
        
        $response->headers->setCookie(
            new Cookie(
                'BEARER',
                $token,
                time() + (3600 * 24), // expire after 1 day
                '/',
                null,
                false,
                true // HttpOnly flag
            )
        );

        return $response;
    }


       /**
     * @Route("/register", name="register", methods={"GET"})
     */
    public function register(): Response
    {
        return $this->render('security/register.html.twig', [
            'error' => "",
            'email' => "",
            'nom' => "",
            'prenom' => "",
            'password' => "",
            'confirmPassword' => "",
        ]);
        
    }
       /**
     * @Route("/register", name="register_account", methods={"POST"})
     */
    public function create_user(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager,JWTTokenManagerInterface $JWTManager, UserRepository $userRepository, RoleRepository $roleRepository): Response
    {
        
        $nom = "";
        $prenom = "";
        $email = "";
        $password = "";
        
        try{
            $nom = $request->request->get('nom', '');
            $prenom = $request->request->get('prenom', '');
            $email = $request->request->get('email', '');
            $password = $request->request->get('password', '');
            
            $errors = [];

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Veuillez saisir une adresse e-mail valide';
        }

        if (strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
        }

       
        $existingUser = $userRepository->findOneBy(['email' => $email]);
        if ($existingUser !== null) {
            $errors[] = 'Un utilisateur avec cette adresse e-mail existe déjà.';
        }

        if (!empty($errors)) {
            return $this->render('security/register.html.twig', [
                'errors' => $errors,
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => $password,
                'error' => "",
            ]);
        }

        $user = new User();
        $user->setNom($nom);
        $user->setPrenom($prenom);
        $user->setEmail($email);
        $role = $roleRepository->findOneBy(['label' => 'user']);
        $user->setRole($role);
        $hashedPassword = $passwordHasher->hashPassword($user, $password);

        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();

        $userData = [
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'nom' => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'role' => $user->getRole(), 
        ];

        $token = $JWTManager->createFromPayload($user,$userData);
    
        $response = new RedirectResponse("/");
        $response->headers->setCookie(
                new Cookie(
                    "BEARER",
                    $token,
                    new \DateTime("+1 day"),
                )
            );
        return $response;

        }catch(Exception $e){
            dd($e);
            return $this->render('security/register.html.twig', [
                'error' => $e->getMessage(),
                'nom' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => $password,
            ]);
        }
    }


    /**
     * @Route("/forgot-password", name="forgot_password_post", methods={"POST"})
     */

public function forgotPassword_post(Request $request, UserRepository $userRepository, TokenGeneratorInterface $tokenGenerator, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
{
        $email = $request->request->get('email');
        
        try{

       
        $user = $userRepository->findOneBy(['email' => $email]);
        if ($user === null) {
            $this->addFlash('success', 'Le lien de réinitialisation du mot de passe a été envoyé par email');
            return new RedirectResponse("/auth/forgot");
        }

        $token = $tokenGenerator->generateToken();
        $user->setResetToken($token);
        $user->setTokenExpiration(new \DateTime('+1 hour'));
        $entityManager->flush();
        
     

        $email_template = (new Email())
            ->from('hello@example.com')
            ->to('you@example.com')
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $response = $mailer->send($email_template);
        
        $this->addFlash('success', 'Le lien de réinitialisation du mot de passe a été envoyé par email');
        return new RedirectResponse("/auth/forgot");
         } catch (\Exception $e) {
            dd($e);
            return $this->render('security/forgot.html.twig', [
                'error' => $e->getMessage(),
                'email' => $email,
            ]);
        }
    }

    /**
     * @Route("/reset-password", name="reset_password")
     */
    public function resetPassword(Request $request): Response
    {
        // Handle reset password request using the $request object
        // ...

        return $this->render('security/reset_password.html.twig', [
            'controller_name' => 'AuthController',
            'email' => $request->get('email'),
            'new_password' => $request->get('new_password'),
            'confirm_password' => $request->get('confirm_password'),
            'token' => $request->get('token'),
        ]);
    }


    /**
 * @Route("/reset-password/{token}", name="reset_password", methods={"POST", "GET"})
 */
public function resetPassword_post(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordEncoder, EntityManagerInterface $entityManager, string $token): Response
{
    $user = $userRepository->findOneBy(['resetToken' => $token]);
    
    if (!$user || !$userRepository->isResetTokenValid($token)) {
        $this->addFlash('error', 'Invalid token');
        return new RedirectResponse("/auth/forgot");
    }
    
    if ($request->isMethod('POST')) {
        $password = $request->request->get('password');
        
        if (!$password) {
            $this->addFlash('error', 'Password cannot be empty');
            return $this->redirectToRoute('reset_password', ['token' => $token]);
        }
        
        $user->setPassword($passwordEncoder->hashPassword($user, $password));
        $user->setResetToken("");
        $user->setTokenExpiration(null);
        $entityManager->flush();
        
        $this->addFlash('success', 'Your password has been reset successfully');
        return $this->redirectToRoute('app_login');
    }
    
    return $this->render('security/reset.html.twig', [
            'error' => "",
            'password' => "",
            "token" => $token,
        ]);
}

        /**
     * @Route("/forgot", name="forgot", methods={"GET"})
     */
    public function forgot_password(): Response
    {
        return $this->render('security/forgot.html.twig', [
            'error' => "",
            'email' => "",
            'nom' => "",
            'prenom' => "",
            'password' => "",
            'confirmPassword' => "",
        ]);
        
    }

    /**
    * @Route("/logout", name="user_logout", methods={"GET"})
    */
    public function logout(Request $request): Response
    {
      
        $response = new RedirectResponse("/auth/login");
        $response->headers->setCookie(
                new Cookie(
                    "BEARER",
                    null,
                    new \DateTime("-1 day"),
                )
            );
        return $response;
    }


  

}
