<?php 
// src/EventListener/AdminRouteListener.php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Collections\Collection;

class AdminRouteListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        $isAdminRoute = strpos($routeName, 'admin') !== false;
        if ($isAdminRoute) {
            $user = $request->attributes->get('user');
            if (!$user || in_array('ROLE_ADMIN',$user['roles'])) {
                throw new AccessDeniedHttpException('Vous n\'avez pas les droits pour accéder à cette page');
            }
        }
        
    }   
}
