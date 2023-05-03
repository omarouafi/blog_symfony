<?php
// src/EventListener/JwtUserListener.php

namespace App\EventListener;

use App\Repository\UserRepository;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtUserListener implements EventSubscriberInterface
{
    private $jwtEncoder;
    private $userRepository;

    public function __construct(JWTEncoderInterface $jwtEncoder, UserRepository $userRepository)
    {
        $this->jwtEncoder = $jwtEncoder;
        $this->userRepository = $userRepository;
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
        
        $is_auth = strpos($routeName, 'auth');
        
        if($is_auth != false){
            return;
        }
        $user_protected = strpos($routeName, 'user');
        
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
            
            
        }else {
            
            if($user_protected !== false){
                $response = new RedirectResponse('/auth/login');
                $event->setResponse($response);
            }else{
                return;
            }
        }
    }catch( \Exception $e){
        if($user_protected !== false){
            
            $response = new RedirectResponse('/auth/login');
            $event->setResponse($response);
        }else{
           return;
        }
    }
    }
}
