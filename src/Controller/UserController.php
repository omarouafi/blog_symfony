<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{


      /**
     * @Route("/profile", name="user_profile", methods={"GET"})
     */
    public function profile(Request $request): Response
    {
        $user = $request->attributes->get('user');
        
        return $this->render('security/profile.html.twig', [
            'user' => $user,
        ]);
        
    }

     /**
      * @Route("/user/edit/me", name="user_edit_me", methods={"POST"})
      */
      public function edit_me(UserRepository $userRepository, Request $request): Response
    {
        try{
        $user = $request->attributes->get('user');
        $id = $user['id'];
        $user = $userRepository->find($id);
        $user->setRoles([$request->get('role')]);
        $user->setNom($request->get('nom'));
        $user->setPrenom($request->get('prenom')); 
        $userRepository->save($user,true);
        return $this->redirect("/profile");
        }catch( Exception $e){
            dd($e);
            $this->addFlash('error', 'Une erreur est survenue');
            return $this->redirect("/profile");    
        }
    }
    /**
      * @Route("/delete/me", name="user_delete_user", methods={"GET"})
      */
    public function delete_me(UserRepository $userRepository, Request $request): Response
    {
        $id = $request->attributes->get('user')['id'];
        $user = $userRepository->find($id);
        $userRepository->remove($user,true);
        return $this->redirectToRoute("/auth/logout");
    }

   /**
      * @Route("/update-my-email", name="user_update_email", methods={"POST"})
      */
    public function update_my_email(UserRepository $userRepository, Request $request): Response
    {
        $id = $request->attributes->get('user')['id'];
        $user = $userRepository->find($id);
        $user_email_exist = $userRepository->findOneBy(['email' => $request->get('email')]);
        if($user_email_exist){
            $this->addFlash('error', 'Cet email est déjà utilisé');
            return $this->redirect("/profile");
        }
        $user->setEmail($request->get('email'));
        $userRepository->save($user,true);
        return $this->redirect("/profile");
    }

    /**
        * @Route("/update-my-password", name="user_update_password", methods={"POST"})
        */
    public function update_my_password(UserRepository $userRepository,  Request $request,UserPasswordHasherInterface $passwordEncoder): Response
    {
        try{        
        $id = $request->attributes->get('user')['id'];
        $user = $userRepository->find($id);

        $old_password = $request->get('old_password');
        $new_password = $request->get('password');

        if(!$passwordEncoder->isPasswordValid($user, $old_password)){
            $this->addFlash('error', 'L\'ancien mot de passe est incorrect');
            return $this->redirectToRoute("admin_show_user",["id"=>$id]);
        }

        $user->setPassword($passwordEncoder->hashPassword($user, $new_password));

        $userRepository->save($user);
        return $this->redirectToRoute("admin_show_user",["id"=>$id]);
        }catch(\Exception $e){
            $this->addFlash('error', 'Une erreur est survenue');
            return $this->redirectToRoute("admin_show_user",["id"=>$id]);
        }
    }

   /**
     * @Route("/admin/users", name="admin_list_users", methods={"GET"})
     */
    public function index(UserRepository $userRepository,Request $request, PaginatorInterface $paginator): Response
    {
        $usersQuery = $userRepository->createQueryBuilder('u')
        ->getQuery();
    
        $pagination = $paginator->paginate(
            $usersQuery,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('user/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }
    /**
      * @Route("/admin/user/{id}", name="admin_show_user", methods={"GET"})
      */
    public function show(UserRepository $userRepository, $id): Response
    {
        $user = $userRepository->find($id);
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
    /**
      * @Route("/admin/user/{id}/edit", name="admin_edit_user", methods={"GET"})
      */
    public function edit(UserRepository $userRepository, $id): Response
    {
        $user = $userRepository->find($id);
        return $this->render('user/edit.html.twig', [
            'user' => $user,
        ]);
    }
    /**
      * @Route("/admin/user/{id}/edit", name="admin_edit_user", methods={"POST"})
      */
    public function edit_user(UserRepository $userRepository, $id, Request $request): Response
    {
        try{
        $user = $userRepository->find($id);
        $user->setRoles([$request->get('role')]);
        $user->setNom($request->get('nom'));
        $user->setPrenom($request->get('prenom'));        
        $userRepository->save($user);
        return $this->redirectToRoute("admin_list_users");
        }catch( Exception $e){
            $this->addFlash('error', 'Une erreur est survenue');
            return $this->redirectToRoute("admin_edit_user",["id"=>$id]);    
        }
    }
    /**
      * @Route("/admin/user/{id}/delete", name="admin_delete_user", methods={"GET"})
      */
    public function delete(UserRepository $userRepository, $id): Response
    {
        $user = $userRepository->find($id);
        $userRepository->remove($user,true);
        return $this->redirectToRoute("admin_list_users");
    }

   /**
      * @Route("/admin/user/{id}/update-email", name="admin_update_email", methods={"POST"})
      */
    public function update_email(UserRepository $userRepository, $id, Request $request): Response
    {
        $user = $userRepository->find($id);
        $user_email_exist = $userRepository->findOneBy(['email' => $request->get('email')]);
        if($user_email_exist){
            $this->addFlash('error', 'Cet email est déjà utilisé');
            return $this->redirectToRoute("admin_show_user",["id"=>$id]);
        }
        $user->setEmail($request->get('email'));
        $userRepository->save($user);
        return $this->redirectToRoute("admin_show_user",["id"=>$id]);
    }

    /**
        * @Route("/admin/user/{id}/update-password", name="admin_update_password", methods={"POST"})
        */
    public function update_password(UserRepository $userRepository, $id, Request $request,UserPasswordHasherInterface $passwordEncoder): Response
    {
        try{        
        $user = $userRepository->find($id);

        $old_password = $request->get('old_password');
        $new_password = $request->get('password');

        if(!$passwordEncoder->isPasswordValid($user, $old_password)){
            $this->addFlash('error', 'L\'ancien mot de passe est incorrect');
            return $this->redirectToRoute("admin_show_user",["id"=>$id]);
        }

        $user->setPassword($passwordEncoder->hashPassword($user, $new_password));

        $userRepository->save($user);
        return $this->redirectToRoute("admin_show_user",["id"=>$id]);
        }catch(\Exception $e){
            $this->addFlash('error', 'Une erreur est survenue');
            return $this->redirectToRoute("admin_show_user",["id"=>$id]);
        }
    }
}
