<?php
// src/EventListener/JwtUserListener.php

namespace App\EventListener;

use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Component\HttpFoundation\Request;
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
        $cookie = $request->cookies->get('BEARER');

        if ($cookie) {
            $data = $this->jwtEncoder->decode($cookie);
            $user['id'] = $data['id'];
            $user['nom'] = $data['nom'];
            $user['prenom'] = $data['prenom'];
            $user['email'] = $data['username'];
            $user['roles'] = $data['roles'];
            $request->attributes->set('user', $user);
        }
    }catch( \Exception $e){
        return;
    }
    }
}
