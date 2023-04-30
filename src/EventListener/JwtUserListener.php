<?php
// src/EventListener/JwtUserListener.php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\User\UserInterface;

class JwtUserListener
{
    private $jwtEncoder;

    public function __construct(JWTEncoderInterface $jwtEncoder)
    {
        $this->jwtEncoder = $jwtEncoder;
    }

    public function onKernelRequest(RequestEvent $event)
    {
        try{

        $request = $event->getRequest();        
        if (strpos($request->getRequestUri(),'auth') !== false  ) {
            return;
        }
        
        $cookie = $request->cookies->get('BEARER');
        if ($cookie) {
            $data = $this->jwtEncoder->decode($cookie);
            $user['id'] = $data['id'];
            $user['nom'] = $data['nom'];
            $user['prenom'] = $data['prenom'];
            $user['email'] = $data['username'];
            $user['roles'] = $data['roles'];
            $request->attributes->set('user', $user);
        }else{
            $response = new RedirectResponse('/auth/login');
            $event->setResponse($response);
        }
    }catch( \Exception $e){
        $response = new RedirectResponse('/auth/login');
        $event->setResponse($response);
    }
    }
}
