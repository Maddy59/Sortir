<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class VilleController extends AbstractController
{
    /**
     * @Route("/indexVille", name="ville_indexVille", methods={"GET"})
     */
    public function index(VilleRepository $villeRepository): Response
    {
        return $this->render('ville/indexVille.html.twig', [
            'ville' => $villeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/ajout", name="ville_ajout", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('ville_indexVille');
        }

        return $this->render('ville/ajoutVille.html.twig', [
            'ville' => $ville,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="ville_voir", methods={"GET"})
     */
    public function show(Ville $ville): Response
    {
        return $this->render('ville/voir.html.twig', [
            'ville' => $ville,
        ]);
    }

    /**
     * @Route("/{id}/modifier", name="ville_modifier", methods={"GET","POST"})
     */
    public function edit(Request $request, Ville $ville): Response
    {
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ville_indexVille');
        }

        return $this->render('ville/modifier.html.twig', [
            'ville' => $ville,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/supprimer", name="ville_supprimer", methods={"DELETE"})
     */
    public function delete(Request $request, Ville $ville): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ville->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ville);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ville_indexVille');
    }


}
