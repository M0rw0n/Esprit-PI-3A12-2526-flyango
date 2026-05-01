<?php

namespace App\Service;

use App\Entity\Circuit;

class CircuitManager
{
    public function validate(Circuit $circuit): bool
    {
        if (empty($circuit->getTitre())) {
            throw new \InvalidArgumentException('Le titre est obligatoire');
        }

        if (strlen($circuit->getTitre()) > 200) {
            throw new \InvalidArgumentException('Le titre ne doit pas dépasser 200 caractères');
        }

        $validTypes = [Circuit::TYPE_CIRCUIT, Circuit::TYPE_SEJOUR, Circuit::TYPE_ACTIVITY];

        if (!in_array($circuit->getType(), $validTypes, true)) {
            throw new \InvalidArgumentException('Type de circuit invalide');
        }

        if ($circuit->getPrix() <= 0) {
            throw new \InvalidArgumentException('Le prix doit être positif');
        }

        return true;
    }
}
