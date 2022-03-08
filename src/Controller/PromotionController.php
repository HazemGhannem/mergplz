<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use App\Entity\Promotion;
use App\Form\PromotionType;
use App\Repository\PromotionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Urlizer;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/")
 */
class PromotionController extends AbstractController
{
    /**
     * @Route("admin/promotion/stats", name="stats")
     */
    public function statistiques(PromotionRepository $promotionRepository)
    { $promotion = $promotionRepository->findAll();
        /* you can also inject "FooRepository $repository" using autowire */
         
       /* $count = $repository->count();
        dd($count); */
       
           /*  $countbydate= $repository->createQueryBuilder('a')
            ->select('SUBSTRING(datefin,1,10) As datedufin, COUNT(a) as count')
            ->groupby('datedufin')
            ->getQuery()
            ->getResult(); */
       //
       $repository = $this->getDoctrine()->getRepository(Promotion::class);
       $count= $repository->createQueryBuilder('u')
            ->select('count(u.datefin)')
            ->groupby('u.datefin')
            ->getQuery()
            ->getResult();
            
            $countdate= $repository->createQueryBuilder('a')
            ->select('(a.datefin)')
            ->groupby('a.datefin')
            ->getQuery()
            ->getResult();
        foreach($promotion as $promotion){
            $pourcentage[] = $promotion->getPourcentage();
          
            $date[] = $promotion->getDatefin()->format('d-m-Y');
            
        }
        
 
            for ($i = 0; $i < count($count); ++$i){
                
                $count1[] = $count[$i][1] ;  
                $countdate1[] = $countdate[$i][1];
            }
            
        
        return $this->render('promotion/stats.html.twig', [ 
            'pourcentage' => json_encode($pourcentage),
            'date' => json_encode($date ),
            'count1' => json_encode($count1),
            'countdate1' => json_encode($countdate1),
            
          
            
        ]);   
    }
    /**
     * @Route("resto/promotion/", name="promotion_index", methods={"GET"})
     */
    public function index(PromotionRepository $promotionRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $promotion = $promotionRepository->findAll();
          $promotion = $paginator->paginate(
        $promotion, /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
        4   /*limit per page*/
    );
        return $this->render('promotion/index.html.twig', [
            'promotions' => $promotion,
        ]);
    }
    /**
     * @Route("profile/promotion/search/{id}", name="search_id", methods={"GET"})
     */
    public function search(Promotion $promotion): Response
    {
        return $this->render('promotion/showfront.html.twig', [
            'promotion' => $promotion,
        ]);
    }
     /**
   * Creates a new ActionItem entity.
   *
   * @Route("profile/promotion/search", name="ajax_search")
   * @Method("GET")
   */
  public function searchAction(Request $request)
  {
      $em = $this->getDoctrine()->getManager();

      $requestString = $request->get('q');

      $promotion =  $em->getRepository(promotion::class)->findEntitiesByString($requestString);

      if(!$promotion) {
          $result['promotion']['error'] = "No Promotion for $requestString  :')";
      } else {
          $result['promotion'] = $this->getRealEntities($promotion);
      }

      return new Response(json_encode($result));
  }

  public function getRealEntities($promotion){

      foreach ($promotion as $promotion){
          $realEntities[$promotion->getId()] = [$promotion->getImage() , $promotion->getNom()];
      } 

      return $realEntities;
  }
    
    /**
     * @Route("profile/promotion", name="promotion_front", methods={"GET"})
     */
    public function promotionfront(PromotionRepository $promotionRepository, PaginatorInterface $paginator, Request $request): Response
    {    
        $promotion = $promotionRepository->findAll();
        $promotion = $paginator->paginate(
            $promotion, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            9   /*limit per page*/
        );
        return $this->render('promotion/promotionfront.html.twig', [
            'promotions' => $promotion ,
            
        ]);
    }

    /**
     * @Route("resto/promotion/new", name="promotion_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $promotion = new Promotion();
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFilename
            );
            $promotion->setImage($newFilename);
            $promotion->setPrixpromotion( $promotion->getPrixoriginal()-($promotion->getPrixoriginal()* $promotion->getPourcentage())/100);
            $entityManager->persist($promotion);
            $entityManager->flush();

            return $this->redirectToRoute('promotion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('promotion/new.html.twig', [
            'promotion' => $promotion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("resto/promotion/{id}", name="promotion_show", methods={"GET"})
     */
    public function show(Promotion $promotion): Response
    {
        return $this->render('promotion/show.html.twig', [
            'promotion' => $promotion,
        ]);
    }
    /**
     * @Route("profile/promotion/{id}", name="show_front", methods={"GET"})
     */
    public function show_front(Promotion $promotion): Response
    {
        return $this->render('promotion/showfront.html.twig', [
            'promotion' => $promotion,
        ]);
    }

    /**
     * @Route("resto/promotion/edit/{id}", name="promotion_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Promotion $promotion, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PromotionType::class, $promotion);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();
            $destination = $this->getParameter('kernel.project_dir').'/public/uploads';
            $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
            $newFilename = Urlizer::urlize($originalFilename).'-'.uniqid().'.'.$uploadedFile->guessExtension();
            $uploadedFile->move(
                $destination,
                $newFilename
            );
                $promotion->setImage($newFilename);
            
                $entityManager = $this->getDoctrine()->getManager();
            $entityManager->flush();

            return $this->redirectToRoute('promotion_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('promotion/edit.html.twig', [
            'promotion' => $promotion,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("resto/promotion/{id}", name="promotion_delete", methods={"POST"})
     */
    public function delete(Request $request, Promotion $promotion, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$promotion->getId(), $request->request->get('_token'))) {
            $entityManager->remove($promotion);
            $entityManager->flush();
        }

        return $this->redirectToRoute('promotion_index', [], Response::HTTP_SEE_OTHER);
    }
   
    
}
