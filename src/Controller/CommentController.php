<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Security\CustomAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CommentController extends AbstractController
{
    /**
     * @Route("/comments", name="list_comments", methods={"GET"})
    */
    public function index(Request $request,CustomAuthenticator $auth, CommentRepository $commentRepository): Response
    {
        $comments = $commentRepository->findAllWithArticleAndAuthor();
        dd($comments);
        return $this->render('comment/comments-list.html.twig', [
            'comments' => $comments,
        ]);
    }
}
