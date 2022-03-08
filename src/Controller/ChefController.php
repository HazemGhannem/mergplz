<?php

namespace App\Controller;

use App\Entity\Chef;
use App\Form\ChefType;
use App\Repository\ChefRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/")
 */
class ChefController extends AbstractController
{
    /**
     * @Route("resto/chef/", name="chef_index", methods={"GET"})
     */
    public function index(ChefRepository $chefRepository,PaginatorInterface $paginator,Request $request): Response
    {
        $var = $this->getDoctrine()
        ->getRepository(Chef::class) -> findall();
        $var = $paginator->paginate(
            $var, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            2/*limit per page*/
        );
        return $this->render('chef/index.html.twig', [
            'chefs' => $var
        ]);

    }

    /**
     * @Route("/jsonCh", name="jsonCh_index", methods={"GET"})
     */
    public function jsonChindex(ChefRepository $chefRepository, SerializerInterface $serializer): Response
    {
        $result = $chefRepository->findAll();
        /* $n = $normalizer->normalize($result, null, ['groups' => 'pack:read']);
        $json = json_encode($n); */
        $json = $serializer->serialize($result, 'json', ['groups' => 'chef:read']);
        return new JsonResponse($json, 200, [], true);
    }

    /**
     * @Route("resto/chef/new", name="chef_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $chef = new Chef();
        $form = $this->createForm(ChefType::class, $chef);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($chef);
            $entityManager->flush();

            return $this->redirectToRoute('chef_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chef/new.html.twig', [
            'chef' => $chef,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("resto/chef/{id}", name="chef_show", methods={"GET"})
     */
    public function show(Chef $chef): Response
    {
        return $this->render('chef/show.html.twig', [
            'chef' => $chef,
        ]);
    }

    /**
     * @Route("resto/chef/edit/{id}", name="chef_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Chef $chef, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ChefType::class, $chef);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('chef_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('chef/edit.html.twig', [
            'chef' => $chef,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("resto/chef/{id}", name="chef_delete", methods={"POST"})
     */
    public function delete(Request $request, Chef $chef, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$chef->getId(), $request->request->get('_token'))) {
            $entityManager->remove($chef);
            $entityManager->flush();
        }

        return $this->redirectToRoute('chef_index', [], Response::HTTP_SEE_OTHER);
    }
}
