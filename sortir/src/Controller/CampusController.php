<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CampusController extends AbstractController
{
    /**
     * @Route("/campus", name="campus_lister", methods={"GET"})
     */
    public function lister(CampusRepository $villeRepository): Response
    {
        return $this->render('campus/listeCampus.html.twig', [
            'campus' => $villeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/campus/ajouter", name="campus_ajouter", methods={"GET","POST"})
     */
    public function ajouter(Request $request): Response
    {
        $campus = new Campus();
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($campus);
            $entityManager->flush();

            return $this->redirectToRoute('campus_lister');
        }

        return $this->render('campus/ajoutCampus.html.twig', [
            'campus' => $campus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/campus/{id}", name="campus_detail", methods={"GET"})
     */
    public function detail(Campus $campus): Response
    {
        return $this->render('campus/detailCampus.html.twig', [
            'campus' => $campus,
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="campus_modifier", methods={"GET","POST"})
     */
    public function modifier(Request $request, Campus $campus): Response
    {
        $form = $this->createForm(CampusType::class, $campus);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('campus_lister');
        }

        return $this->render('campus/modifier.html.twig', [
            'campus' => $campus,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/supprimer", name="campus_supprimer", methods={"DELETE"})
     */
    public function supprimer(Request $request, Campus $campus): Response
    {
        if ($this->isCsrfTokenValid('delete'.$campus->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($campus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('campus_lister');
    }


}
