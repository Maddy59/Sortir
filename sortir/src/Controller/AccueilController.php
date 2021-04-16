<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Form\SearchFormSortie;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/", name="accueil_accueil")
     */
    public function accueil(Request $request,
                            SortieRepository $sortieRepository,
                            UserRepository $userRepository,
                            CampusRepository $campusRepository,
                            EtatRepository $etatRepository,
                            EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();

        $data = new SearchData();
        $formSortie = $this->createForm(SearchFormSortie::class, $data);


        $formSortie->handleRequest($request);

        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            $sorties = $sortieRepository->findSearch($data, $user);
            foreach ($sorties as $sortie){
                if (new dateTime("now") > $sortie->getDateLimiteInscription()){
                    $etat = $etatRepository->find(3);
                    $sortie->setEtat($etat);
                    $entityManager->persist($etat);
                    $entityManager->flush();
                }
                if (new dateTime("now") == $sortie->getDateHeureDebut()){
                    $etat = $etatRepository->find(4);
                    $sortie->setEtat($etat);
                    $entityManager->persist($etat);
                    $entityManager->flush();
                }
                if (new dateTime("now") > $sortie->getDateHeureDebut()){
                    $etat = $etatRepository->find(5);
                    $sortie->setEtat($etat);
                    $entityManager->persist($etat);
                    $entityManager->flush();
                }
            }
        } else {
            $sorties = $sortieRepository->findAll();
            foreach ($sorties as $sortie){
                if (new dateTime("now") > $sortie->getDateLimiteInscription()){
                    $etat = $etatRepository->find(3);
                    $sortie->setEtat($etat);
                    $entityManager->persist($etat);
                    $entityManager->flush();
                }
                if (new dateTime("now") == $sortie->getDateHeureDebut()){
                    $etat = $etatRepository->find(4);
                    $sortie->setEtat($etat);
                    $entityManager->persist($etat);
                    $entityManager->flush();
                }
                if (new dateTime("now") > $sortie->getDateHeureDebut()){
                    $etat = $etatRepository->find(5);
                    $sortie->setEtat($etat);
                    $entityManager->persist($etat);
                    $entityManager->flush();
                }
            }
//            dd($sorties);
        }

        return $this->render('accueil/accueil.html.twig', [
            'sorties' => $sorties,
            'user' => $user,
            'formSortie' => $formSortie->createView(),
        ]);
    }

}
