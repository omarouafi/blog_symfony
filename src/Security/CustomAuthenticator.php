<?php 


namespace App\Security;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;

class CustomAuthenticator 
{

    
    public function supports(Request $request)
    {
        return $request->headers->has('Authorization') && strpos($request->headers->get('Authorization'), 'Bearer ') === 0;
    }
    
    public static function authenticate(Request $request)
    {
        $tokenString = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        
        try {
            $token = JWTTokenManagerInterface->decode($tokenString);
        } catch (\Exception $e) {
            throw new CustomUserMessageAuthenticationException('Invalid JWT token: ' . $e->getMessage());
        }
        
        $user = new User(); 
        $user->setEmail($token->email); 
        $user->setRole($token->role); 
        
        return $user;
    }
    
  
}