<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/profile/home", name="home")
     */
    public function index(): Response
    {
        return $this->render('front.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
    /**
     * @Route("/employee", name="home")
     */
    public function index1(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
     /**
     * @Route("/test", name="home")
     */
    public function test(): Response
    {
        return $this->render('base.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
 
    
   

    
}
