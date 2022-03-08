<?php

namespace App\Controller;

use App\Entity\Pack;
use App\Form\PackType;
use App\Repository\PackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/")
 */
class PackController extends AbstractController
{
    /**
     * @Route("admin/pack", name="pack_index", methods={"GET"})
     */
    public function index(PackRepository $packRepository, PaginatorInterface $paginator, Request $request): Response
    { $pack = $packRepository->findAll();
        $pack= $paginator->paginate(
      $pack, /* query NOT result */
      $request->query->getInt('page', 1), /*page number*/
     4  /*limit per page*/
  );
        return $this->render('pack/index.html.twig', [
            'packs' => $pack,
        ]);
    }
    
      /**
     * @Route("resto/pack", name="pack", methods={"GET"})
     */
    public function pack(PackRepository $packRepository, PaginatorInterface $paginator, Request $request): Response
    {$pack = $packRepository->findAll();
        $pack =$paginator->paginate( 
      $pack, /* query NOT result */
      $request->query->getInt('page', 1), /*page number*/
      2   /*limit per page*/
  );
        return $this->render('pack/pack.html.twig', [
            'packs' => $pack,
        ]);
    }

    /**
     * @Route("admin/pack/new", name="pack_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $pack = new Pack();
        $form = $this->createForm(PackType::class, $pack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($pack);
            $entityManager->flush();

            return $this->redirectToRoute('pack_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pack/new.html.twig', [
            'pack' => $pack,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/pack/{id}", name="pack_show", methods={"GET"})
     */
    public function show(Pack $pack): Response
    {
        return $this->render('pack/show.html.twig', [
            'pack' => $pack,
        ]);
    }

    /**
     * @Route("admin/pack/edit/{id}", name="pack_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Pack $pack, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PackType::class, $pack);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
             
            $entityManager->flush();

            return $this->redirectToRoute('pack_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('pack/edit.html.twig', [
            'pack' => $pack,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("admin/pack/{id}", name="pack_delete", methods={"POST"})
     */
    public function delete(Request $request, Pack $pack, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$pack->getId(), $request->request->get('_token'))) {
            $entityManager->remove($pack);
            $entityManager->flush();
        }

        return $this->redirectToRoute('pack_index', [], Response::HTTP_SEE_OTHER);
    }
}
