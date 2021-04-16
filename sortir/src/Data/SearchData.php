<?php


namespace App\Data;


use App\Entity\Campus;
use Symfony\Component\Validator\Constraints as Assert;


class SearchData
{
    /**
     * @var Campus
     */
    public $campus;

    /**
     * @var string
     */
    public $recherche = '';

    /**
     * @var Sortie[]
     */
    public $categories = [];

    /**
     * @var Date|null
     *
     */
    public $dateDebut;

    /**
     * @var Date|null
     *  @Assert\GreaterThanOrEqual(propertyPath="dateDebut", message="la date doit etre posterieur a la date de debut")
     */
    public $dateFin;

}