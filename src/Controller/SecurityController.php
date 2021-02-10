<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;
use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/users", name="users_list")
     */
    public function users(UserRepository $userRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $users = $userRepository->getUsers();

        return $this->render('security/users.html.twig', ['users' => $users]);
    }

    /**
     * @Route("/user/add", name="add_user")
     * @Route("/user/edit/{id}", name="edit_user", requirements={"id" = "\d+"})
     */
    public function edit(Request $request, UserRepository $userRepository, $id = null)
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (is_null($id)) {
            $entity = new User;
        } else {
            $entity = $userRepository->find($id);
        }

        $form  = $this->createForm(UserType::class, $entity);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $entityManager = $this->getDoctrine()->getManager();
                $entity->setPassword($this->passwordEncoder->encodePassword($entity, $form->getData()->getPassword()));
                $entityManager->persist($entity);
                $entityManager->flush();
                $this->get('session')->getFlashBag()->add('success', 'User has been successfully saved!');
                
                return $this->redirect($this->generateUrl('users_list'));
            }
        }

        return $this->render('security/editUser.html.twig', [
            'form'  =>  $form->createView(),
            'item'  =>  $entity
        ]);
    }
}
