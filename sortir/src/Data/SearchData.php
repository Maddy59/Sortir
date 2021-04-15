<?php


namespace App\Data;


use App\Entity\Campus;
use App\Entity\Sortie;



class SearchData
{
    /**
     * @var Campus
     */
    public Campus $campus;

    /**
     * @var string
     */
    public string $recherche = '';

    /**
     * @var Sortie[]
     */
    public array $categories = [];

    /**
     * @var Date
     */
    public Date $dateDebut;

    /**
     * @var Date
     */
    public Date $dateCloture;

}