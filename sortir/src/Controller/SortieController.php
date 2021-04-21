<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\AnnulerSortieForm;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UserRepository;
use App\Services\ObjetDansArray;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
        $sortie->setOrganisateur($this->getUser());
        $sortie->setCampus($this->getUser()->getCampus());
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);
        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setArchivee(false);
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
    public function inscription($id, ObjetDansArray $objetDansArray, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        $sortie = $sortieRepository->find($id);
        if (count($sortie->getParticipants()) < $sortie->getNbInscriptionsMax() && !$objetDansArray->existsInArray($user, $sortie->getParticipants())) {
            if ($sortie->getEtat() != 'Ouverte') {
                $this->addFlash('echec', 'Les inscriptions ne sont plus ouvertes pour cette sortie');
            } else {
                $sortie->addParticipant($user);
                $entityManager->flush();
                $this->addFlash('success', 'vous vous etes inscrits pour la sortie');
            }
        } else {
            $this->addFlash('echec', "vous etes deja inscrit dans la sortie");
        }


        return $this->redirectToRoute('accueil_accueil');
    }

    /**
     * @Route("/desistement/{id}", name="desistement")
     */
    public function desistement($id, ObjetDansArray $objetDansArray, SortieRepository $sortieRepository, EntityManagerInterface $entityManager, UserRepository $userRepository, Request $request): Response
    {
        $user = $this->getUser();
        $sortie = $sortieRepository->find($id);
        if ($objetDansArray->existsInArray($user, $sortie->getParticipants())) {
            if ($sortie->getEtat() != 'Ouverte' || $sortie->getEtat() != 'Annulée') {
                $this->addFlash('echec', "Vous ne pouvez pas vous désinscrire car les inscriptions sont fermées");
            } else {
                $this->addFlash('success', 'vous vous etes désinscrit de la sortie');
                $sortie->removeParticipant($user);
                $entityManager->flush();
            }
        } else {
            $this->addFlash('echec', 'Echec: Vous ne pouvez pas vous désisncrire');
        }

        return $this->redirectToRoute('accueil_accueil');
    }

    /**
     * @Route("/afficher/{id}", name="afficher")
     */
    public function afficher($id, SortieRepository $sortieRepository)
    {
        $sortie = $sortieRepository->find($id);

        return $this->render('sortie/afficher.html.twig', [
            'sortie' => $sortie
        ]);
    }

    /**
     * @Route("/annuler/{id}", name="annuler")
     */
    public function annuler($id, Sortie $sortie, SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $sortie = $sortieRepository->find($id);

        if($this->getUser()->getUsername() != $sortie->getOrganisateur()->getUsername()){
            return new Response(500);
        }
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
