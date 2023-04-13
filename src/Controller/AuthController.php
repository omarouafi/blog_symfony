<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;


   /**
     * @Route("/auth", name="auth")
    */
class AuthController extends AbstractController
{
   
    /**
     * @Route("/login", name="login")
    */
    public function login(): Response
    {
        $error = "";
        return $this->render('security/index.html.twig', [
            'error' => $error,
            'email' => '',
            'password' => '',

        ]);
    }
       /**
     * @Route("/register", name="register", methods={"GET"})
     */
    public function register(): Response
    {
        return $this->render('security/register.html.twig', [
            'error' => "",
            'email' => "",
            'password' => "",
            'confirmPassword' => "",
        ]);
        
    }
       /**
     * @Route("/register", name="create_user", methods={"POST"})
     */
    public function create_user(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        
        $email = "";
        $password = "";
        try{
            $data=json_decode($request->getContent());
            $email = $data->email;
            $password = $data->password;
            
            $errors = [];

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            dd($email);
            $errors[] = 'Veuillez saisir une adresse e-mail valide';
        }

        if (strlen($password) < 6) {
            $errors[] = 'Le mot de passe doit contenir au moins 6 caractères';
        }

       
        $userRepository = $entityManager->getRepository(User::class);
        $existingUser = $userRepository->findOneBy(['email' => $email]);
        if ($existingUser !== null) {
            $errors[] = 'Un utilisateur avec cette adresse e-mail existe déjà.';
        }

        if (!empty($errors)) {
            dd($errors);
            return $this->render('security/register.html.twig', [
                'errors' => $errors,
                'email' => $email,
                'password' => $password,
            ]);
        }

        $user = new User();
        $user->setEmail($email);
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $passwordHasher->hashPassword($user, $password);

        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();
        
        return $this->redirectToRoute('login');
    

        }catch(Exception $e){

            dd($e);
            return $this->render('security/register.html.twig', [
                'error' => $e->getMessage(),
                'email' => $email,
                'password' => $password,
            ]);
        }
    }

    /**
     * @Route("/forgot-password", name="forgot_password")
     */
    public function forgotPassword(Request $request): Response
    {
        // Handle forgot password request using the $request object
        // ...

        return $this->render('security/forgot_password.html.twig', [
            'controller_name' => 'AuthController',
            'email' => $request->get('email'),
        ]);
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
}
