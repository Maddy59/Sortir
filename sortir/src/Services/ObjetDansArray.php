<?php


namespace App\Services;


class ObjetDansArray
{
    /*
     * Prends n'importe quel objet et retourne vrai si l'objet
     * existe dans l'array sinon retourne false
     */

    public function existsInArray($object, $array)
    {
        foreach ($array as $compare) {
            if ($compare === $object) {
                return true;
            }
        }
        return false;
    }
}