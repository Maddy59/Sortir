<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /**
     * Recupère le sorties par une query
     * @return Sortie[]
     */
    public function findSearch(SearchData $search): array
    {
        $query = $this->createQueryBuilder('s')
            ->select('s', 'c')
            ->join('s.campus', 'c');

        if ($search->recherche != "") {
            $query = $query
                ->andWhere("s.nom like :q")
                ->setParameter('q', "%{$search->recherche}%");
        }

        if (!empty($search->campus)) {
            $query = $query
                ->andWhere("c.id = :q")
                ->setParameter('q', $search->campus->getId());
        }





//                dd($query->getQuery());


        return $query->getQuery()->execute();
    }
}
