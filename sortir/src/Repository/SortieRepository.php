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
            ->select('s', 'e')
            ->join('s.etat', 'e')
            ->andWhere('e.libelle not like :libelle')
            ->setParameter('libelle', 'Passee')
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
            ->select('s', 'c', 'e')
            ->join('s.campus', 'c')
            ->join('s.etat', 'e');

        if ($search->recherche != "") {
            $query = $query
                ->andWhere("s.nom like :recherche")
                ->setParameter('recherche', "%{$search->recherche}%");
        }

        if (!empty($search->campus)) {
            $query = $query
                ->andWhere("c.id = :campus")
                ->setParameter('campus', $search->campus->getId());
        }
        if (in_array('organisateur', $search->categories)) {
            $query = $query
                ->andWhere("s.organisateur = :organisateur")
                ->setParameter('organisateur', $user);
        }
        if (in_array('passes', $search->categories)) {
            $query = $query
                ->andWhere('e.libelle like :libelle')
                ->setParameter('libelle', 'Passee');
        } else {
            $query = $query
                ->andWhere('e.libelle not like :libelle')
                ->setParameter('libelle', 'Passee');
        }

        $query = $query->orderBy('s.dateHeureDebut', 'DESC');

        $resultat = $query->getQuery()->execute();

        if (in_array('inscrit', $search->categories)) {
            $resultat = array_filter($resultat, function ($user) {
                if (isset($user)) {
                    return true;
                }
                return false;
            });
        }
        if (in_array('non-inscrit', $search->categories)) {
            $resultat = array_filter($resultat, function ($user) {
                if (isset($user)) {
                    return false;
                }
                return true;
            });
        }


        return $resultat;
    }
}
