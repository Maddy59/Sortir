<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Form\SearchFormSortie;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/accueil", name="accueil_accueil")
     */
    public function index(Request $request, SortieRepository $sortieRepository, UserRepository $userRepository, CampusRepository $campusRepository): Response
    {

        $user = $userRepository->findOneBy(['id' => 1]);

        $sorties = $sortieRepository->findAll();
        $data = new SearchData();
        $formSortie = $this->createForm(SearchFormSortie::class, $data);


        $formSortie->handleRequest($request);

        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            $sorties = $sortieRepository->findSearch($data, $user);

        }

        return $this->render('accueil/accueil.html.twig', [
            'sorties' => $sorties,
            'user' => $user,
            'formSortie' => $formSortie->createView(),
        ]);
    }

}
