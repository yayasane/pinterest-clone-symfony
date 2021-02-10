<?php

namespace App\Controller;

use App\Entity\Pin;
use App\Form\PinType;
use App\Repository\PinRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PinsController extends AbstractController
{
    /**
     * @Route("/", name="app_home", methods={"GET"})
     * @param PinRepository $pinRepository
     * @return Response
     */
    public function index(PinRepository $pinRepository): Response
    {
        $pins = new Pin();
        $pins = $pinRepository->findBy([], ['created_at' => 'desc']);
        return $this->render('pins/index.html.twig', compact('pins'));
    }

    /**
     * @Route("/pins/create", name="app_pins_create", methods={"GET", "POST"})
     * @IsGranted("PIN_CREATE")
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pin = new Pin;
        $form = $this->createForm(PinType::class, $pin);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pin->setUser($this->getUser());
            $em->persist($pin);
            $em->flush();
            $this->addFlash('success', 'Pin succesfully created');
            return $this->redirectToRoute('app_home');
        }
        return $this->render("pins/create.html.twig", ['form' => $form->createView()]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}/edit", name="app_pins_edit", methods={"GET","PUT"})
     * @return Response
     */
    public function edit(Pin $pin, Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted("PIN_MANAGE", $pin);
        $form = $this->createForm(PinType::class, $pin, ['method' => 'put']);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Pin successfully edited');
            return $this->redirectToRoute('app_home');
        }

        return $this->render("pins/edit.html.twig", ['form' => $form->createView(), 'pin' => $pin]);
    }

    /**
     * @Route("/pins/{id<[0-9]+>}/delete", name="app_pins_delete", methods={"DELETE"})
     * @return Response
     * @IsGranted("PIN_MANAGE", subject="pin")
     */
    public function delete(Request $request, Pin $pin, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('yeah' . $pin->getId(), $request->request->get('comedown'))) {
            # code...
            $em->remove($pin);
            $em->flush();
            $this->addFlash('info', 'Pin successfully deleted');
        }
        return $this->redirectToRoute('app_home');
    }

    /**
     * @Route("/pins/{id<[0-9]+>}", name="app_pins_show", methods={"GET"})
     * @param Pin $pin
     * @return Response
     */
    public function show(Pin $pin): Response
    {
        return $this->render("pins/show.html.twig", compact("pin"));
    }
}
