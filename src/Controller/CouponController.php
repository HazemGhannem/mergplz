<?php

namespace App\Controller;

use App\Entity\Coupon;
use App\Form\CouponType;
use App\Repository\CouponRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("admin/coupon")
 */
class CouponController extends AbstractController
{
    /**
     * @Route("/", name="coupon_index", methods={"GET"})
     */
    public function index(CouponRepository $couponRepository, PaginatorInterface $paginator, Request $request): Response
    { $coupon = $couponRepository->findAll();
        $coupon = $paginator->paginate(
      $coupon, /* query NOT result */
      $request->query->getInt('page', 1), /*page number*/
      4   /*limit per page*/
  );
        return $this->render('coupon/index.html.twig', [
            'coupons' => $coupon,
        ]);
    }
    
        
        
       
   

    /**
     * @Route("/new", name="coupon_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager,MailerInterface $mailer): Response
    {
        $coupon = new Coupon();
        $form = $this->createForm(CouponType::class, $coupon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($coupon);
            $entityManager->flush();
            $email= (new TemplatedEmail())
            ->from('floussflouss766@gmail.com')
            ->to('nivav27132@toudrum.com')
            ->subject('You win a Coupon with FlyFood ❤️')
            ->htmlTemplate('coupon/email.html.twig')
            ->context(['coupon' => $coupon,]);
            $mailer -> send($email);
            return $this->redirectToRoute('coupon_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('coupon/new.html.twig', [
            'coupon' => $coupon,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/pdf/{id}", name="coupon_pdf", methods={"GET"})
     */ 
    public function pdf(Coupon $coupon): Response
    {    $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('coupon/pdffile.html.twig', [
            'coupon' => $coupon,
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A6', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("CouponFlyFood.pdf", [
            "Attachment" => true
        ]);
    }

    /**
     * @Route("/{id}", name="coupon_show", methods={"GET"})
     */
    public function show(Coupon $coupon): Response
    {
        return $this->render('coupon/show.html.twig', [
            'coupon' => $coupon,
            ]);
        }

    /**
     * @Route("/edit/{id}", name="coupon_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Coupon $coupon, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CouponType::class, $coupon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('coupon_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('coupon/edit.html.twig', [
            'coupon' => $coupon,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="coupon_delete", methods={"POST"})
     */
    public function delete(Request $request, Coupon $coupon, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$coupon->getId(), $request->request->get('_token'))) {
            $entityManager->remove($coupon);
            $entityManager->flush();
        }

        return $this->redirectToRoute('coupon_index', [], Response::HTTP_SEE_OTHER);
    }
}
