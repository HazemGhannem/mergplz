<?php

namespace App\Controller;

use App\Entity\Plat;

use App\Entity\Utilisateur;
use App\Entity\Categorie;
use App\Form\PlatType;
use App\Repository\PlatRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Urlizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Mediumart\Orange\SMS\SMS;
use Mediumart\Orange\SMS\Http\SMSClient;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

/**
 * @Route("/")
 */
class PlatController extends AbstractController
{
    /**
     * @Route("resto/plat/", name="plat_index", methods={"GET"})
     */
    public function index(PlatRepository $platRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $var = $this->getDoctrine()
        ->getRepository(plat::class) -> findall();
        $var = $paginator->paginate(
            $var, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6/*limit per page*/
        );
        return $this->render('plat/index.html.twig', [
            'plats'=> $var,
        ]);
    }

     /**
     * @Route("/jsonP", name="jsonP_index", methods={"GET"})
     */
    public function jsonPindex(PlatRepository $platRepository ,SerializerInterface $serializer): Response
    {
        $result = $platRepository->findAll();
        /* $n = $normalizer->normalize($result, null, ['groups' => 'pack:read']);
        $json = json_encode($n); */
        $json = $serializer->serialize($result, 'json', ['groups' => 'plat:read']);
        return new JsonResponse($json, 200, [], true);
    }

    
    /**
     * @Route("resto/plat/new", name="plat_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager, MailerInterface $mailer): Response
    {
        $plat = new Plat();
        $user = $this->getDoctrine()->getRepository(Utilisateur::class)->findAll();
        $form = $this->createForm(PlatType::class, $plat);
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
                $plat->setImg($newFilename);
            $entityManager->persist($plat);
            $entityManager->flush();
            foreach ($user as $user ){
                $client = SMSClient::getInstance('nEoxkRRL52MtHzUNAoaXc0ngnNVl9KDC', 'zSB1YIu2CSwoLnBL');
                $sms = new SMS($client);
                $sms->message('Salut '.$user->getNom().',
Nous vous invitons à gouter notre nouveau 🍛plat🍛 
Nom plat = '.$plat->getNom().'🥳
Description = '.$plat->getDescription().'🥳
N hesitez pas à vister notre site FlyFood pour plus d infos 🥳')
            ->from('+21651464577')
            ->to($user->getNumTel())
            ->send();
            }
           
            $email= (new TemplatedEmail())
            ->from('floussflouss766@gmail.com')
            ->to('osdj@gh.com')
            ->subject('🥳 Un nouveau 🍛plat🍛 débarque sur 🥳FlyFood🥳')
            ->htmlTemplate('plat/email.html.twig')
            ->context([
                'plat' => $plat,
            ]);
            
            $mailer -> send($email);

            return $this->redirectToRoute('plat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('plat/new.html.twig', [
            'plat' => $plat,
            'form' => $form->createView(),
        ]);
    }
    
    
    /**
     * @Route("resto/plat/{id}", name="plat_show", methods={"GET"})
     */
    public function show(Plat $plat): Response
    {
        return $this->render('plat/show.html.twig', [
            'plat' => $plat,
        ]);
    }
    

    /**
     * @Route("resto/plat/edit/{id}/", name="plat_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Plat $plat, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PlatType::class, $plat);
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
                $plat->setImg($newFilename);
            
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->flush();

            return $this->redirectToRoute('plat_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('plat/edit.html.twig', [
            'plat' => $plat,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("resto/plat/{id}", name="plat_delete", methods={"POST"})
     */
    public function delete(Request $request, Plat $plat, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$plat->getId(), $request->request->get('_token'))) {
            $entityManager->remove($plat);
            $entityManager->flush();
        }

        return $this->redirectToRoute('plat_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("profile/Search/{id}", name="platshow")
     */ 
    public function platshow(Plat $plat): Response
    {
        
        return $this->render('plat/platid.html.twig', [
            'plat' => $plat,
        ]);
    }


    /**
     * @Route("profile/Search/", name="ajax_searchP",methods={"GET"})
     */
    public function searchActionC(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $plats =  $em->getRepository(plat::class)->findEntitiesByString($requestString);
        if(!$plats) {
            $result['plats']['error'] = "Désolés, nous n'avons trouvé aucun résultat pour $requestString 😔 ";
        } else {
            $result['plats'] = $this->getRealEntities($plats);
        }
        return new Response(json_encode($result));
    }
    public function getRealEntities($plats){
        foreach ($plats as $plats){
            $realEntities[$plats->getId()] = [$plats->getImg(),$plats->getNom()];

        }
        return $realEntities;
    }
    
    
    /**
     * @Route("profile/platid/{id}", name="platcat", methods={"GET"})
     */
    public function platcat($id,PaginatorInterface $paginator,Request $request): Response
    {
        $categorie=$this->getDoctrine()->getRepository(Categorie::class)->find($id);
        $plat=$this->getDoctrine()->getRepository(Plat::class)
        ->findAll();
        $tab= array();
    
        foreach ($plat as $plat){
            if($plat->getCategorie()==$categorie)
            {

                array_push($tab, $plat);

            }
           
         
        }
        $tab = $paginator->paginate(
            $tab, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6/*limit per page*/
        );

        return $this->render('plat/platcat.html.twig', 
            ['plat' => $tab]);
    }

     /**
     * @Route("profile/platf", name="plat_f", methods={"GET"})
     */
    public function froindex(PlatRepository $platRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $var = $this->getDoctrine()
        ->getRepository(plat::class) -> findall();
        $var = $paginator->paginate(
            $var, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            6/*limit per page*/
        );
        return $this->render('plat/platshow.html.twig', [
            'var'=> $var,
        ]);
    }
    

   
   

    
}