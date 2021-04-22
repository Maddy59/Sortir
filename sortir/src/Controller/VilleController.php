<?php

namespace App\Controller;

use App\Entity\Ville;
use App\Form\SearchFormVilleType;
use App\Form\VilleType;
use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class VilleController extends AbstractController
{
    /**
     * @Route("/villes", name="ville_lister")
     */
    public function lister(VilleRepository $villeRepository,Request $request ): Response
    {
       $villes = [];
       $ville = new Ville();
       $formVille = $this->createForm(SearchFormVilleType::class, $ville);
        $formVille->handleRequest($request);
        if ($formVille ->isSubmitted()&& $formVille->isValid()){
            $villes= $villeRepository->rechercheVille($ville);

        }else {
            $villes= $villeRepository->findAll();
        }

        return $this->render('ville/listeVilles.html.twig', [
            'ville' => $villes,
            'formVille' =>$formVille->createView(),
        ]);
    }

    /**
     * @Route("/ville/ajouter", name="ville_ajouter", methods={"GET","POST"})
     */
    public function ajouter(Request $request): Response
    {
        $ville = new Ville();
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($ville);
            $entityManager->flush();

            return $this->redirectToRoute('ville_lister');
        }

        return $this->render('ville/ajoutVille.html.twig', [
            'ville' => $ville,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ville/{id}", name="ville_detail", methods={"GET"})
     */
    public function detail(Ville $ville): Response
    {
        return $this->render('ville/detailVille.html.twig', [
            'ville' => $ville,
        ]);
    }

    /**
     * @Route("/ville/modifier/{id}", name="ville_modifier", methods={"GET","POST"})
     */
    public function modifier(Request $request, Ville $ville): Response
    {
        $form = $this->createForm(VilleType::class, $ville);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('ville_lister');
        }

        return $this->render('ville/modifier.html.twig', [
            'ville' => $ville,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/ville/supprimer/{id}", name="ville_supprimer", methods={"DELETE"})
     */
    public function supprimer(Request $request, Ville $ville): Response
    {
        if ($this->isCsrfTokenValid('delete'.$ville->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($ville);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ville_lister');
    }

}
