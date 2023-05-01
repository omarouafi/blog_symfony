<?php 
// src/EventListener/AdminRouteListener.php

namespace App\EventListener;

use App\Repository\ArticleRepository;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MonArticleRouteListener implements EventSubscriberInterface
{

    private $articleRepository;

    public function __construct(ArticleRepository $articleRepository)
    {
        $this->articleRepository = $articleRepository;
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
    public function onKernelRequest(RequestEvent $event)
    {
        try{

        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        $isMyArticleRoute = strpos($routeName, 'mon_article') !== false;
        if (!$isMyArticleRoute) {
            return;
        }
        $article_id = $request->attributes->get('id');
        
        $article = $this->articleRepository->find($article_id);
        $user = $request->attributes->get('user');
        if ($isMyArticleRoute) {
            if (!$user || $article->getAuthor()->getId() != $user['id']) {
                throw new AccessDeniedHttpException('Vous n\'avez pas les droits pour accéder à cette page');
            }
        }

    }catch( \Exception $e){
            $response = new RedirectResponse('/home');
            $event->setResponse($response);
        }
    }   
}
