<?php

namespace App\Controller;

use App\Entity\Evennement;
use App\Form\EvennementType;
use App\Repository\EvennementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/evennement")
 */
class EvennementController extends AbstractController
{
    /**
     * @Route("/", name="evennement_index", methods={"GET"})
     */
    public function index(EvennementRepository $evennementRepository): Response
    {
        return $this->render('evennement/index.html.twig', [
            'evennements' => $evennementRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="evennement_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $evennement = new Evennement();
        $form = $this->createForm(EvennementType::class, $evennement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($evennement);
            $entityManager->flush();

            return $this->redirectToRoute('evennement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evennement/new.html.twig', [
            'evennement' => $evennement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evennement_show", methods={"GET"})
     */
    public function show(Evennement $evennement): Response
    {
        return $this->render('evennement/show.html.twig', [
            'evennement' => $evennement,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="evennement_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Evennement $evennement, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EvennementType::class, $evennement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('evennement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('evennement/edit.html.twig', [
            'evennement' => $evennement,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="evennement_delete", methods={"POST"})
     */
    public function delete(Request $request, Evennement $evennement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$evennement->getId(), $request->request->get('_token'))) {
            $entityManager->remove($evennement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('evennement_index', [], Response::HTTP_SEE_OTHER);
    }
}
