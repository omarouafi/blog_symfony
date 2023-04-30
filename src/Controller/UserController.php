<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
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
}
