<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Form\SearchFormSortie;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use DateTime;
use App\Services\MiseAJourEtatSorties;
use App\Services\ArchivageSorties;
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
                            EtatRepository $etatRepository,
                            EntityManagerInterface $entityManager,
                            MiseAJourEtatSorties $etatSorties,
                            ArchivageSorties $archivageSorties): Response
    {

        $user = $this->getUser();

        $data = new SearchData();
        $formSortie = $this->createForm(SearchFormSortie::class, $data);


        $formSortie->handleRequest($request);

        if ($formSortie->isSubmitted() && $formSortie->isValid()) {
            $sorties = $sortieRepository->findSearch($data, $user);
        } else {
            $sorties = $sortieRepository->findAll();
        }

        $etatSorties->miseAJourEtatSorties($sorties, $etatRepository, $entityManager);
        $archivageSorties->archivage($sorties, $entityManager);

        return $this->render('accueil/accueil.html.twig', [
            'sorties' => $sorties,
            'formSortie' => $formSortie->createView(),
        ]);
    }

}
