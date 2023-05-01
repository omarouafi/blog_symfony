<?php

namespace App\Controller;

use App\Security\CustomAuthenticator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

 /**
     * @Route("/", name="home")
    */
class HomeController extends AbstractController
{
    
     /**
     * @Route("/", name="home")
    */
    public function index(Request $request,CustomAuthenticator $auth): Response
    {
       return $this->redirectToRoute('list_articles');
    }
}
