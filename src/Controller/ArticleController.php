<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Security\CustomAuthenticator;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
   /**
     * @Route("/articles", name="list_articles", methods={"GET"})
    */
    public function index(Request $request,CustomAuthenticator $auth, ArticleRepository $articleRepository): Response
    {
        $articles = $articleRepository->findAllWithAuthorCommentsTags();
        
        return $this->render('articles/articles-list.html.twig', [
            'articles' => $articles,
        ]);
    }
   /**
     * @Route("/article/create", name="create_articles", methods={"GET"})
    */
    public function new(Request $request,CustomAuthenticator $auth, ArticleRepository $articleRepository): Response
    {
        
        return $this->render('articles/article-create.html.twig', [
            "error" => "",
            "title" => "",
            "author_id" => "",
            "content" => "",
        ]);
    }
   /**
     * @Route("/article/create", name="create_articles_post", methods={"POST"})
    */
    public function new_post(Request $request,UserRepository $userRepository,CustomAuthenticator $auth, ArticleRepository $articleRepository): Response
    {
       try{
            $title = $request->get('title', '');
            $content = $request->get('content', '');
            $user = $request->attributes->get('user');
            $author = $userRepository->find($user['id']);
            if (!$title || !$content) {
                return $this->render('articles/article-create.html.twig', [
                    "error" => "Le titre et le contenu sont obligatoires",
                    "title" => $title,
                    "content" => $content,
                ]);
            }
            $article = new Article();
            $article->setTitle($title);
            $article->setContent($content);
            $article->setAuthor($author);
            $article->setStatus(0);
            $articleRepository->save($article, true);
            return $this->redirectToRoute('list_articles');

        } catch(Exception $e) {
                dd($e);
                return $this->render('articles/article-create.html.twig', [
                    "error" => "Une erreur est survenue",
                    "title" => $title,
                    "content" => $content,
                ]);
            
        } 
            
        }
    /**
    * @Route("/articles/{id}", name="article_detail")
    */
    public function articleDetail(Article $article)
    {
        return $this->render('articles/detail.html.twig', [
            'article' => $article
        ]);
    }
   /**
 * @Route("/article/{id}/comment", name="article_commenter")
 */
    public function article_commenter(Request $request ,Article $article, UserRepository $userRepository,CommentRepository $commentRepository,)
    {
        $content = $request->request->get('comment_content');
        $user = $request->attributes->get('user');
        $author = $userRepository->find($user['id']);
        
        $comment = new Comment();
        $comment->setContent($content);
        $comment->setAuthor($author);
        $comment->setArticleId($article);
        
        $commentRepository->save($comment, true);
        return $this->redirectToRoute('article_detail', ['id' => $article->getId()]);

        
    }
   /**
 * @Route("/article/{id}/modifier", name="article_modifier", methods={"GET"})
 */
    public function article_modifier(Request $request ,Article $article)
    {
        return $this->render('articles/article-edit.html.twig', [
            'article' => $article
        ]);
    }
   /**
 * @Route("/article/{id}/modifier", name="article_modifier_post", methods={"POST"})
 */
    public function article_modifier_post(Request $request ,Article $article,ArticleRepository $articleRepository)
    {
        $title = $request->request->get('title');
        $content = $request->request->get('content');
        $article->setTitle($title);
        $article->setContent($content);
        $articleRepository->save($article, true);
        return $this->redirectToRoute('article_detail', ['id' => $article->getId()]);        
    }
}
