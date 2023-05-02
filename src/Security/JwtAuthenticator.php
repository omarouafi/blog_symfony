<?php
namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


class JwtAuthenticator 
{
    private $jwtManager;
    
    public function __construct(JWTTokenManagerInterface $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }
    
    public function supports(Request $request)
    {
        return $request->headers->has('Authorization') && strpos($request->headers->get('Authorization'), 'Bearer ') === 0;
    }
    
    public function authenticate(Request $request)
    {
        $tokenString = str_replace('Bearer ', '', $request->headers->get('Authorization'));
        
        try {
            $token = $this->jwtManager->decode($tokenString);
        } catch (\Exception $e) {
            throw new CustomUserMessageAuthenticationException('Invalid JWT token: ' . $e->getMessage());
        }
        
        $user = new User(); // Replace with your User class
        $user->setEmail($token->email); // Replace with your user data
        $user->setRole($token->role); // Replace with your user data
        
        return new UsernamePasswordToken($user, $tokenString, 'jwt', $user->getRole());
    }
    
  
}
