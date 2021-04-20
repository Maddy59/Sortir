<?php


namespace App\Services;


use App\Entity\SortieArchivee;
use App\Repository\EtatRepository;
use Doctrine\ORM\EntityManagerInterface;

class MiseAJourEtatSorties
{
    /**
     * Mise à jour de l'état des sorties
     */
    public function miseAJourEtatSorties(array $sorties, EtatRepository $etatRepository, EntityManagerInterface $entityManager): void
    {
        foreach ($sorties as $sortie) {
            if (new \dateTime("now") > $sortie->getDateLimiteInscription()) {
                $etat = $etatRepository->find(3);
                $sortie->setEtat($etat);
                $entityManager->persist($etat);
                $entityManager->flush();
            }
            if (new \dateTime("now") == $sortie->getDateHeureDebut()) {
                $etat = $etatRepository->find(4);
                $sortie->setEtat($etat);
                $entityManager->persist($etat);
                $entityManager->flush();
            }
            if (new \dateTime("now") > $sortie->getDateHeureDebut()) {
                $etat = $etatRepository->find(5);
                $sortie->setEtat($etat);
                $entityManager->persist($etat);
                $entityManager->flush();
            }

            if ($sortie->getDateHeureDebut() < new \DateTime("- 30 days")) {
                $sortie->setArchivee(true);
                $entityManager->flush();
            }
        }
    }
}
