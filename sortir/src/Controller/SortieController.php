<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Sortie;
use App\Form\AnnulerSortieForm;
use App\Form\SearchFormSortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
        EtatRepository $etatRepository): Response {

        $sortie = new Sortie();
        $sortie->setOrganisateur($this->getUser());
        $sortie->setCampus($this->getUser()->getCampus());
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            if ($sortieForm->get('enregistrer')->isClicked()) {
                $etatDefaut = $etatRepository->find(1);
                $sortie->setEtat($etatDefaut);
                $entityManager->persist($etatDefaut);

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('succes', 'Votre sortie a bien été créée.');
                return $this->redirectToRoute('accueil_accueil');
            }
            if ($sortieForm->get('publier')->isClicked()) {
                $etatDefaut = $etatRepository->find(2);
                $sortie->setEtat($etatDefaut);
                $entityManager->persist($etatDefaut);

                $entityManager->persist($sortie);
                $entityManager->flush();

                $this->addFlash('succes', 'Votre sortie a bien été publiée.');
                return $this->redirectToRoute('accueil_accueil');
            }
        }
        return $this->render('sortie/creer.html.twig', [
            'sortieForm' => $sortieForm->createView(),
        ]);
    }

    /**
     * @Route("/inscription/{id}", name="inscription")
     */
    public function inscription($id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        $sortie = $sortieRepository->find($id);
        $sortie->addParticipant($user);
        $entityManager->flush();
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

    /**
     * @Route("/desistement/{id}", name="desistement")
     */
    public function desistement($id, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        $sortie = $sortieRepository->find($id);
        $sortie->removeParticipant($user);
        $entityManager->flush();

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

    /**
     * @Route("/annuler/{id}", name="annuler")
     */
    public function annuler(Sortie $sortie, EtatRepository $etatRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(AnnulerSortieForm::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $etat = $etatRepository->findByLibelle("Annulé");
            $sortie->setEtat($etat);
            $em->persist($sortie);
            $em->flush();

        }
        return $this->render('sortie/annuler.html.twig', [
            'AnnulerSortieForm' => $form->createView(),
        ]);

    }

}
