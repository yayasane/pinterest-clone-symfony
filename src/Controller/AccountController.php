<?php

namespace App\Controller;

use App\Form\ChangePasswordFormType;
use App\Form\UserFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/account")
 * @IsGranted("ROLE_USER")
 */
class AccountController extends AbstractController
{
    /**
     * @Route("", name="app_account", methods="GET")
     * @IsGranted("ROLE_USER")
     */
    public function show(): Response
    {
        return $this->render('account/show.html.twig', [
            'controller_name' => 'AccountController',
        ]);
    }
    /**
     * @Route("/edit", name="app_account_edit", methods={"GET","PUT"})
     * @IsGranted("IS_AUTHENTICATED_FULLY")
     */
    public function edit(Request $request, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserFormType::class, $user, [
            'method' => 'put',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Account updated succesfully!');
            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/change-password", name="app_account_change_password", methods="GET|PUT")
     * @IsGranted("IS_AUTENTICATED_FULLY")
     */
    public function changePassword(
        Request $request,
        UserPasswordEncoderInterface $passwordEncoder
    ): Response {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class, null, [
            'method' => 'put',
            'current_password_is_required' => true,
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $passwordEncoder->encodePassword(
                    $user,
                    $form['plainPassword']->getData()
                )
            );
            $this->getDoctrine()
                ->getManager()
                ->flush();
            $this->addFlash('success', 'Password updated succesfully!');
            return $this->redirectToRoute('app_account');
        }
        return $this->render('account/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
