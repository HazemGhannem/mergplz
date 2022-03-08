<?php

namespace App\Controller;

use App\Entity\CommandeItem;
use App\Form\CommandeItemType;
use App\Repository\CommandeItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/commande/item")
 */
class CommandeItemController extends AbstractController
{
    /**
     * @Route("/", name="commande_item_index", methods={"GET"})
     */
    public function index(CommandeItemRepository $commandeItemRepository): Response
    {
        return $this->render('commande_item/index.html.twig', [
            'commande_items' => $commandeItemRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="commande_item_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commandeItem = new CommandeItem();
        $form = $this->createForm(CommandeItemType::class, $commandeItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commandeItem);
            $entityManager->flush();

            return $this->redirectToRoute('commande_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande_item/new.html.twig', [
            'commande_item' => $commandeItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="commande_item_show", methods={"GET"})
     */
    public function show(CommandeItem $commandeItem): Response
    {
        return $this->render('commande_item/show.html.twig', [
            'commande_item' => $commandeItem,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="commande_item_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, CommandeItem $commandeItem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeItemType::class, $commandeItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('commande_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('commande_item/edit.html.twig', [
            'commande_item' => $commandeItem,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="commande_item_delete", methods={"POST"})
     */
    public function delete(Request $request, CommandeItem $commandeItem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commandeItem->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commandeItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('commande_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
