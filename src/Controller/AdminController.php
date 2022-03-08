<?php

namespace App\Controller;

use App\Entity\User;
use AppBundle\Repository\UserRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class AdminController extends AbstractController
{

    /**
    * 
    * @Route("/admin", name="admin_list")
    */
    public function admin(Request $request ,PaginatorInterface $paginator)
    {
        
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $users = $paginator->paginate(
            $users, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            10 /*limit per page*/
        );
        
        
        return $this->render('admin/index.html.twig', [
            'users' => $users
            
        ]);
    }
    /*public function add(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        # only admin function
    }

    // ...*/
     /**
     * @Route("/admin/Delete/{id}" ,name="DELETE_USER")
     *Method({"DELETE"})
     */
    public function Delete(Request $request,$id)
    {
            $User = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($User);
            $entityManager->flush();

            
            return $this->redirectToRoute('admin_list');
            
    }
       /**
     * @Route("/admin/update/{id}" ,name="BLOCK_USER")
     *Method({"GET", "POST"})
     */
    public function Block(Request $request,$id)
    {       
            $User = new User();
            $User = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);

            $form = $this->createformbuilder($User)
            ->add('isExpired',CheckboxType::class, [
                'label'    => 'BLOCK',
                'required' => false,
            ])
            ->add('Done',SubmitType::class)
            ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() ) {
                 $entityManager = $this->getDoctrine()->getManager();
                 $entityManager->flush();

                return $this->redirectToRoute('admin_list');
            }
            return $this->render('admin/update.html.twig', [
                'form' => $form->createView()
               ]);
            
    }

      /**
     * @Route("/admin/role/{id}" ,name="ROLE_ROLES")
     *Method({"GET", "POST"})
     */
    public function ROLE(Request $request,$id)
    {       
            $User = new User();
            $User = $this->getDoctrine()
            ->getRepository(User::class)
            ->find($id);
            #$cat = $User->getRoles();
            $form = $this->createformbuilder($User)
            ->add('roles', ChoiceType::class, array(
                'attr'  =>  array('class' => 'form-control',
                'style' => 'margin:5px 0;'),
                'choices' => 
                array
                (
                    'ROLE_USER' => array
                    (
                        'Yes' => 'ROLE_USER'
                    ),
                    'ROLE_EMPLOYEE' => array
                    (
                        'Yes' => 'ROLE_EMPLOYEE' 
                    ),
                    'ROLE_RESTOWNER' => array
                    (
                        'Yes' => 'ROLE_RESTOWNER'
                    ),
                    
                ) 
                ,
                'multiple' => true,
                'required' => true,
                )
            )
            ->add('Done',SubmitType::class)
            ->getForm();
            $form->handleRequest($request);
            if ($form->isSubmitted() ) {
                 $entityManager = $this->getDoctrine()->getManager();
                 $entityManager->flush();

                return $this->redirectToRoute('admin_list');
            }
            return $this->render('admin/role.html.twig', [
                'form' => $form->createView()
               ]);
            
    }
    /**
     * @Route("/admin/filter" ,name="filter")
     */

    public function listwhereadminfirst(){
        $users=$this->getDoctrine()
                    ->getRepository(User::class)
                    ->findusers();
        return $this->render('admin/test.html.twig', [
                        'users' => $users
                    ]);
    }
    
   
    /**
     * @Route("/admin/{id}", name="user_show", methods={"GET"} , requirements={"id":"\d+"})
     */
    public function show(User $users): Response
    {
        return $this->render('admin/show.html.twig', [
            'users' => $users,
        ]);
    }
    /**
     * @Route("/profile/setting/changepassword", name="changepass", methods={"GET","POST"} ,)
     */
    public function editProfileAction(Request $request,UserPasswordEncoderInterface $encoder) {
        $user = $this->getUser();
        //$oldPassword = $user->getPassword();
        
        
        $entityManager = $this->getDoctrine()->getManager();



        $cp = $this->createformbuilder($user)
            //->add('password',PasswordType::class)
            ->add('plainPassword',  RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        
                    ],
                    'label' => 'New password',
                ],
                'second_options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'label' => 'Repeat Password',
                ],
                'invalid_message' => 'The password fields must match.',
                // Instead of being set onto the object directly,
                // this is read and encoded in the controller
                'mapped' => false,
            ])
            ->add('submit',SubmitType::class)
            ->getForm();
            
            $cp->handleRequest($request);
            if ($cp->isSubmitted() )
            {
                //$op= $request->get('password');
                $p= $request->get('plainPassword');
                     //dd($request->get('password'));
            //$hashedPassword = $encoder->encodePassword($user, $op);
            //$hashedPasswordd = $encoder->encodePassword($user, $p);
            //$hashedP = $encoder->encodePassword(
             //   $user,
             //   $cp->get('password')->getData()
           // );
            $hashedPasswordd = $encoder->encodePassword(
                $user,
                $cp->get('plainPassword')->getData()
            );
           
            //dd( $hashedPasswordd,$hashedP,$oldPassword);
            if ($hashedPasswordd == $hashedPasswordd){ 
                    $user->setPassword($hashedPasswordd);
                    $entityManager->persist($user);
                    $entityManager->flush();
                    
              

            }}

                         
            return $this->render('Setting/cha.html.twig', [
                'cp' => $cp->createView()
               ]);
            
    }
    /**
     * @Route("/profile/setting/pic", name="changepic", methods={"GET","POST"} ,)
     */
    public function editProfilepicAction(Request $request) {
        $user = $this->getUser();
        $oldPic = $user->getImage();  
        $entityManager = $this->getDoctrine()->getManager();
        $pic = $this->createformbuilder($user)
            //->add('password',PasswordType::class)
            ->add('image',FileType::class,[
                'mapped' =>false,
                'label'=> 'please upload your image'
            ])
            ->add('submit',SubmitType::class)
            ->getForm();
            $pic->handleRequest($request);
            
            
            if ($pic->isSubmitted() )
            {
                $file= $pic->get('image')->getData();
                $filename= md5(uniqid()).'.'.$file->guessExtension();
                $file->move($this->getParameter('upload_directory'),$filename);
                $user->setImage($filename);
                    // encode the plain password
                    
                    $entityManager->persist($user);
                    $entityManager->flush();
            }                      
            return $this->render('Setting/pic.html.twig', [
                'pic' => $pic->createView()
               ]);
            
    }
   

 
   
}
