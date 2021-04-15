<?php

namespace App\Controller;

use App\Form\SearchFormSortie;
use App\Repository\CampusRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccueilController extends AbstractController
{
    /**
     * @Route("/accueil", name="accueil_accueil")
     */
    public function index(SortieRepository $sortieRepository, UserRepository $userRepository, CampusRepository $campusRepository): Response
    {

        $user = $userRepository->findOneBy(['id'=>1]);

        $data = new SearchFormSortie();
        $form = $this->createForm(SearchFormSortie::class,$data);

        return $this->render('accueil/index.html.twig', [
            'sorties' => $sorties,
            'user'=>$user,
            'formSortie' => $form->createView(),
        ]);
    }

}
