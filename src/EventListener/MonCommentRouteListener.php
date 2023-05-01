<?php 
// src/EventListener/AdminRouteListener.php

namespace App\EventListener;

use App\Repository\CommentRepository;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\RedirectResponse;

class MonCommentRouteListener implements EventSubscriberInterface
{

    private $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
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
        $isMyCommentRoute = strpos($routeName, 'mon_comment') !== false;
        if (!$isMyCommentRoute) {
            return;
        }
        $comment_id = $request->attributes->get('id');
        
        $comment = $this->commentRepository->find($comment_id);
        $user = $request->attributes->get('user');
        if ($isMyCommentRoute) {
            if (!$user || $comment->getAuthor()->getId() != $user['id']) {
                throw new AccessDeniedHttpException('Vous n\'avez pas les droits pour accéder à cette page');
            }
        }

    }catch( \Exception $e){
            $response = new RedirectResponse('/home');
            $event->setResponse($response);
        }
    }   
}
