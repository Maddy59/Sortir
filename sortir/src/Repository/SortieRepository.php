<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Sortie;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * @return Sortie[]
     * Recupère la liste des sorties dans l'ordre descendantes
     * et ne prends pas en compte les sorties déjà passées
     */
    public function findAll(): array
    {
        $query = $this->createQueryBuilder('s')
            ->select('s', 'e','p')
            ->join('s.etat', 'e')
            ->leftjoin('s.participants', 'p')
            ->andWhere('e.libelle not like :libelle1')
            ->setParameter('libelle1', 'Passee')
            ->andWhere('e.libelle not like :libelle2')
            ->setParameter('libelle2', 'Creee')
            ->orderBy('s.dateHeureDebut', 'DESC');

        return $query->getQuery()->execute();
    }

    /**
     * Recupère le sorties par une query
     * @return Sortie[]
     */
    public function findSearch(SearchData $search, User $user): array
    {

        $query = $this->createQueryBuilder('s')
            ->select('s', 'c', 'e', 'p')
            ->join('s.campus', 'c')
            ->join('s.etat', 'e')
            ->leftjoin('s.participants', 'p')
            ->orderBy('s.dateHeureDebut', 'DESC');
        /**
         * si la barre de recherche n'est pas vide on ajoute
         * les termes de la recherche à notre query
         */
        if ($search->recherche != "") {
            $query = $query
                ->andWhere("s.nom like :recherche")
                ->setParameter('recherche', "%{$search->recherche}%");
        }
        /**
         * si un campus à été choisi  on ajoute
         * le campus  à notre query
         */
        if (!empty($search->campus)) {
            $query = $query
                ->andWhere("c.id = :campus")
                ->setParameter('campus', $search->campus->getId());
        }
        /**
         * si on a coché la case "Je suis organisateur/trice" on ajoute
         * à notre query la clause where organisateur = user
         * et on ajoutera les sorties qui ont etat = crée
         */
        if (in_array('organisateur', $search->categories)) {
            $query = $query
                ->andWhere("s.organisateur = :organisateur")
                ->setParameter('organisateur', $user);
        }
        /**
         * si on a coché la case "Sorties passées" on ajoute
         * à notre query la clause where etat = passé
         * sinon on fait notre query en excluant les etats Passee et Créee
         */
        if (in_array('passes', $search->categories)) {
            $query = $query
                ->andWhere('e.libelle like :libelle')
                ->setParameter('libelle', 'Passee');
        } else {
            $query = $query
                ->andWhere('e.libelle not like :libelle1')
                ->setParameter('libelle1', 'Passee')
                ->andWhere('e.libelle not like :libelle2')
                ->setParameter('libelle2', 'Creee');
        }

        if ($search->dateDebut) {
            $query = $query
                ->andWhere('s.dateHeureDebut >= :dateDebut')
                ->setParameter('dateDebut', $search->dateDebut);
        }

        if ($search->dateFin) {
            $query = $query
                ->andWhere('s.dateHeureDebut <= :dateFin')
                ->setParameter('dateFin', $search->dateFin);
        }




        if (in_array('inscrit', $search->categories)) {
                $query = $query
                    ->andWhere(':participant MEMBER OF s.participants ')
                    ->setParameter('participant', $user);
            //            $resultat = array_filter($resultat, function ($user) {
//                if (isset($user)) {
//                    return true;
//                }
//                return false;
//            });
//            dd($query->getQuery());
        }

        if (in_array('non-inscrit', $search->categories)) {
            $query = $query
                ->andWhere(':participant NOT MEMBER OF s.participants ')
                ->setParameter('participant', $user);
        }

        $resultat = $query->getQuery()->execute();

        return $resultat;
    }
}
