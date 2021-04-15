<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SortieType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SortieController
 * @package App\Controller
 * @Route("/sortie", name="sortie_")
 */
class SortieController extends AbstractController
{
    /**
     * @Route("/creer", name="creer")
     */
    public function creer(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);
        if($sortieForm->isSubmitted()){

            $etatDefaut = new Etat();
            $etatDefaut->setLibelle('Créée');
            $sortie->setEtat($etatDefaut);
            $entityManager->persist($etatDefaut);



            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('succes', 'Votre sortie a bien été créée.');
            return $this->redirectToRoute();
        }

        return $this->render('sortie/creer.html.twig', [
            'sortieForm'=>$sortieForm->createView()
        ]);
    }
}
