<?php

namespace App\Controller;

use App\Repository\CommentRepository;
use App\Security\CustomAuthenticator;
use Exception;
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
        return $this->render('comment/comments-list.html.twig', [
            'comments' => $comments,
        ]);
    }

      /**
    * @Route("/comment/{id}/supprimer", name="comment_supprimer", methods={"GET"})
    */
    public function delete(Request $request, CommentRepository $commentRepository): Response
    {
        try{

        $comment = $commentRepository->find($request->get('id'));
        if (!$comment) {
            throw $this->createNotFoundException(
                'No comment found for id '.$request->get('id')
            );
        }
        $commentRepository->remove($comment,true);
        return $this->redirectToRoute('article_detail', ['id' => $comment->getArticleId()->getId()]);        
    }catch(Exception $e){        
        return $this->redirectToRoute('list_articles');        
        }

    }
}
