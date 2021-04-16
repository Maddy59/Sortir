<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\UserRepository;
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
    public function creer(Request $request,
                          EntityManagerInterface $entityManager,
                          UserRepository $userRepository,
                          EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $user = $userRepository->find(3);
        $sortie->setOrganisateur($user);
        /*$sortie->setOrganisateur($this->getUser());*/
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);
        if($sortieForm->isSubmitted() && $sortieForm->isValid()){
            dd($sortieForm->getData());
            $etatDefaut = $etatRepository->find(1);
            $sortie->setEtat($etatDefaut);
            $entityManager->persist($etatDefaut);

            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('succes', 'Votre sortie a bien été créée.');
            return $this->redirectToRoute('accueil_accueil');
        }
        return $this->render('sortie/creer.html.twig', [
            'sortieForm'=>$sortieForm->createView()
        ]);
    }
}
