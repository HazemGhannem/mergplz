<?php

namespace App\Controller;

use App\Entity\Plat;
use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\PlatRepository;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

use App\Entity\Urlizer;
/**
 * @Route("/")
 */
class CategorieController extends AbstractController
{
    /**
     * @Route("resto/categorie", name="categorie_index", methods={"GET"})
     */
    public function index(CategorieRepository $categorieRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $var = $this->getDoctrine()
        ->getRepository(categorie::class) -> findall();
        $var = $paginator->paginate(
            $var, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            1/*limit per page*/
        );
        return $this->render('categorie/index.html.twig', [
            'var' => $var
        ]);
    }
    


    /**
     * @Route("profile/SearchC", name="ajax_searchC",methods={"GET"})
     */
    public function searchActionC(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $requestString = $request->get('q');
        $categories =  $em->getRepository(categorie::class)->findEntitiesByString($requestString);
        if(!$categories) {
            $result['categories']['error'] = "Désolés, nous n'avons trouvé aucun résultat pour $requestString 😔 ";
        } else {
            $result['categories'] = $this->getRealEntities($categories);
        }
        return new Response(json_encode($result));
    }
    public function getRealEntities($categories){
        foreach ($categories as $categories){
            $realEntities[$categories->getId()] = [$categories->getImage(),$categories->getNom()];

        }
        return $realEntities;
    }
    /**
     * @Route("profile/SearchC/{id}", name="Categshow")
     */
    public function Categshow(Categorie $categorie): Response
    {
        
        return $this->render('categorie/categshow.html.twig', [
            'categorie' => $categorie,
        ]);
    }

    /**
     * @Route("profile/frontcateg/", name="categoriefront_index", methods={"GET"})
     */
    public function indexfront(CategorieRepository $categorieRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $var = $this->getDoctrine()
        ->getRepository(categorie::class) -> findall();
        $var = $paginator->paginate(
            $var, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            1/*limit per page*/
        );


        
        return $this->render('categorie/indexfront.html.twig', [
            // 'form' => $form,
            'var'=> $var,
            
            ]);
        
    }
    /**
     * @Route("/jsonC", name="jsonC_index", methods={"GET"})
     */
    public function jsonCindex(CategorieRepository $categorieRepository, SerializerInterface $serializer): Response
    {
        $result = $categorieRepository->findAll();
        /* $n = $normalizer->normalize($result, null, ['groups' => 'pack:read']);
        $json = json_encode($n); */
        $json = $serializer->serialize($result, 'json', ['groups' => 'categorie:read']);
        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @Route("resto/categorie/new", name="categorie_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $categorie = new Categorie();
        $form = $this->createForm(CategorieType::class, $categorie);
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
                 $categorie->setImage($newFilename);
            $entityManager->persist($categorie);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie/new.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("resto/categorie/{id}", name="categorie_show", methods={"GET"})
     */
    public function show(Categorie $categorie): Response
    {
        return $this->render('categorie/show.html.twig', [
            'categorie' => $categorie,
        ]);
    }
    
    

    /**
     * @Route("resto/categorie/edit/{id}", name="categorie_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategorieType::class, $categorie);
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
                  $categorie->setImage($newFilename);
            $entityManager->flush();

            return $this->redirectToRoute('categorie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('categorie/edit.html.twig', [
            'categorie' => $categorie,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("resto/categorie/{id}", name="categorie_delete", methods={"POST"})
     */
    public function delete(Request $request, Categorie $categorie, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$categorie->getId(), $request->request->get('_token'))) {
            $entityManager->remove($categorie);
            $entityManager->flush();
        }

        return $this->redirectToRoute('categorie_index', [], Response::HTTP_SEE_OTHER);
    }
}
