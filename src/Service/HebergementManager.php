<?php

namespace App\Service;

use App\Entity\Hebergement;

class HebergementManager
{
    public function validate(Hebergement $hebergement): bool
    {
        if (empty($hebergement->getNom())) {
            throw new \InvalidArgumentException('Le nom est obligatoire');
        }

        if (empty($hebergement->getVille())) {
            throw new \InvalidArgumentException('La ville est obligatoire');
        }

        if (empty($hebergement->getType())) {
            throw new \InvalidArgumentException('Le type est obligatoire');
        }

        if ($hebergement->getPrixParNuit() <= 0) {
            throw new \InvalidArgumentException('Le prix doit être positif');
        }

        return true;
    }
}
