<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
<<<<<<< HEAD
=======
use App\Entity\Promotion;

use App\Entity\Plat;
use App\Entity\Categorie;
>>>>>>> yass

class HomeController extends AbstractController
{
    /**
<<<<<<< HEAD
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
 
    
   

    
=======
     * @Route("/", name="home")
     */
    public function index(): Response
    {
        $var = $this->getDoctrine()
        ->getRepository(promotion::class)
        ->findAll();

        
        $var2 = $this->getDoctrine()
        ->getRepository(categorie::class)
        ->findAll();
        $var1 = $this->getDoctrine()
        ->getRepository(plat::class)
        ->findAll();
    
        return $this->render('front.html.twig', [
            'var'=> $var,
            'var2'=> $var2,
        'var1'=> $var1,

        ]);
    }

    /**
     * @Route("/resto", name="back")
     */
    public function back(): Response
    {
        return $this->render('backTemplateResto/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }
>>>>>>> yass
}
