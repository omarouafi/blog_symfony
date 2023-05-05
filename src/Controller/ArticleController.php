<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use App\Security\CustomAuthenticator;
use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Knp\Component\Pager\PaginatorInterface;
use League\HTMLToMarkdown\HtmlConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{
    /**
    * @Route("/articles", name="list_articles", methods={"GET"})
    */
    public function index(Request $request, CustomAuthenticator $auth, ArticleRepository $articleRepository,PaginatorInterface $paginator): Response
    {
        $articlesQuery=null;
        $query = $request->query->get('q');
        if ($query) {
            $articlesQuery = $articleRepository->searchByTitleContentAuthor($query);
        } else {
            $articlesQuery = $articleRepository->findAllWithAuthorCommentsTags();
        }

        $articles = $paginator->paginate(
            $articlesQuery,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10),
            
        );

        

        return $this->render('articles/articles-list.html.twig', [
            'articles' => $articles,
        ]);
    }

     /**
    * @Route("/user/articles", name="user_list_articles", methods={"GET"})
    */
    public function mes_articles(Request $request, CustomAuthenticator $auth, ArticleRepository $articleRepository,PaginatorInterface $paginator, UserRepository $userRepository): Response
    {
        $user = $request->attributes->get('user');
        $user = $userRepository->find($user['id']);
        $articles = $articleRepository->listArticlesByLoggedInUser($user);
        return $this->render('articles/mes-articles-list.html.twig', [
            'articles' => $articles,
        ]);
    }

   /**
     * @Route("/article/create", name="user_create_articles", methods={"GET"})
    */
    public function new(Request $request,CustomAuthenticator $auth,TagRepository $tagRepository, ArticleRepository $articleRepository): Response
    {
        
        $tags = $tagRepository->findAll();
        return $this->render('articles/article-create.html.twig', [
            "error" => "",
            "title" => "",
            "author_id" => "",
            "content" => "",
            "tags" => $tags,
            "image" => '',

        ]);
    }
   /**
     * @Route("/article/create", name="user_create_articles_post", methods={"POST"})
    */
    public function new_post(Request $request,UserRepository $userRepository,TagRepository $tagRepository, ArticleRepository $articleRepository): Response
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
                    "image" => '',
                    "tags" => '',
                ]);
            }
            $article = new Article();
            $article->setTitle($title);
            $article->setContent($content);
            $article->setAuthor($author);
            $article->setStatus(0);
            $article->setCreatedAt(new \DateTime());
            $article->setUpdatedAt(new \DateTime());

            $file = $request->files->get('image');
            if ($file) {
                $fileName = md5(uniqid()) . '.' . $file->guessExtension();
                $file->move($this->getParameter('uploads_directory'), $fileName);
                $article->setImage('/uploads/'.$fileName);
            }

            $tags = $request->get('tags', []);
            foreach ($tags as $tagId) {
                $tag = $tagRepository->find($tagId);
                if ($tag) {
                    $article->addTag($tag);
                }
            }

            $articleRepository->save($article, true);
            return $this->redirectToRoute('list_articles');

        } catch(Exception $e) {
                dd($e);
                return $this->render('articles/article-create.html.twig', [
                    "error" => "Une erreur est survenue",
                    "title" => $title,
                    "content" => $content,
                    "image" => '',
                    "tags" => '',
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
 * @Route("/article/{id}/comment", name="user_article_commenter")
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
        $comment->setCreatedAt(new \DateTime());
        $comment->setUpdatedAt(new \DateTime());

        
        $commentRepository->save($comment, true);
        return $this->redirectToRoute('article_detail', ['id' => $article->getId()]);

        
    }
   /**
 * @Route("/article/{id}/modifier", name="user_mon_article_modifier", methods={"GET"})
 */
    public function article_modifier(Request $request ,Article $article, TagRepository $tagRepository)
    {
        $error = "";
        $tags = $tagRepository->findAll();
        $converter = new HtmlConverter();
        $markdownContent = $converter->convert($article->getContent());

       

        return $this->render('articles/article-edit.html.twig', [
            'error' => $error,
            'article' => $article,
            'markdownContent' => $markdownContent,
            'image' => '',
            "tags" => $tags
        ]);
    }
   /**
 * @Route("/article/{id}/modifier", name="user_mon_article_modifier_post", methods={"POST"})
 */
public function article_modifier_post(Request $request ,Article $article,ArticleRepository $articleRepository, TagRepository $tagRepository)
{
    $title = $request->request->get('title');
    $content = $request->request->get('content');
    $article->setTitle($title);
    $article->setContent($content);
    $file = $request->files->get('image');
    if ($file) {
        $fileName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($this->getParameter('uploads_directory'), $fileName);
        $article->setImage('/uploads/'.$fileName);
    }
    $selectedTags = [];
    $tags = $request->get('tags', []);
    foreach ($tags as $tagId) {
        $tag = $tagRepository->find($tagId);
        if ($tag) {
            $selectedTags[] = $tag;
        }
    }
    $selectedTags = new ArrayCollection($selectedTags);
    $article->setTags($selectedTags);
    $articleRepository->save($article, true);
    return $this->redirectToRoute('article_detail', ['id' => $article->getId()]);
}
   /**
 * @Route("/article/{id}/supprimer", name="user_mon_article_supprimer", methods={"GET"})
 */
    public function article_supprimer(Request $request ,Article $article,ArticleRepository $articleRepository)
    {
        $articleRepository->remove($article, true);
        return $this->redirectToRoute('list_articles');        
    }
}
