<?php


namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

class ArchivageSorties
{
    /**
     * Mise à jour de l'archivage des sorties
     */
    public function archivage(array $sorties, EntityManagerInterface $entityManager): void
    {
        foreach ($sorties as $sortie) {
            if ($sortie->getDateHeureDebut() < new \DateTime("- 30 days")) {
                $sortie->setArchivee(true);
                $entityManager->flush();
            }
        }
    }
}
