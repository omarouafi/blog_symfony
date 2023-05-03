<?php 
// src/EventListener/AdminRouteListener.php

namespace App\EventListener;

use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Doctrine\Common\Collections\Collection;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminRouteListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'kernel.request' => 'onKernelRequest',
        ];
    }
    private $jwtEncoder;
    private $userRepository;
    public function __construct(JWTEncoderInterface $jwtEncoder,UserRepository $userRepository)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;

    }
    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();
        $routeName = $request->attributes->get('_route');
        $isAdminRoute = strpos($routeName, 'admin') !== false;
        if ($isAdminRoute) {
            $cookie = $request->cookies->get('BEARER');
            if ($cookie) {
                $data = $this->jwtEncoder->decode($cookie);
                $user['id'] = $data['id'];
                $updated_user = $this->userRepository->findOneWithRoles($user['id']);
                $user['nom'] = $updated_user->getNom();
                $user['prenom'] = $updated_user->getPrenom();
                $user['email'] = $updated_user->getEmail();
                $user['role'] = $updated_user->getRole();
                $request->attributes->set('user', $user);
            }
            if ($user == null){
                $response = new RedirectResponse('/auth/login');
                $event->setResponse($response);
            }else if (!$user || $user['role']->getLabel() != 'admin') {
                throw new AccessDeniedHttpException('Vous n\'avez pas les droits pour accéder à cette page');
            }
        }
        
    }   
}
