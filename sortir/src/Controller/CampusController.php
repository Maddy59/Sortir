<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Form\SearchFormCampusType;
use App\Repository\CampusRepository;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CampusController extends AbstractController
{
    /**
     * @Route("/campus", name="campus_lister")
     */
    public function lister(CampusRepository $campusRepository, Request $request): Response
    {
        $campusees = [];
        $campus = new Campus();
        $formCampus = $this->createForm(SearchFormCampusType::class, $campus);
        $formCampus->handleRequest($request);
        if ($formCampus->isSubmitted() && $formCampus->isValid()) {
            $campusees = $campusRepository->rechercheCampus($campus);
        } else {
            $campusees = $campusRepository->findAll();
        }

        return $this->render('campus/listeCampus.html.twig', [
            'campus' => $campusees,
            'formCampus' => $formCampus->createView(),
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
        if ($this->isCsrfTokenValid('delete' . $campus->getId(), $request->request->get('_token'))) {
            try {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($campus);
                $entityManager->flush();
            } catch (Exception $e) {
                $this->addFlash("echec", "Ce Campus est associé à des lieux ou des sorties vous ne pouvez pas l'effacer");
            }
        }

        return $this->redirectToRoute('campus_lister');
    }


}
