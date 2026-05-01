<?php

namespace App\Service;

use App\Entity\Activity;

class ActivityManager
{
    public function validate(Activity $activity): bool
    {
        if (empty($activity->getTitle())) {
            throw new \InvalidArgumentException('Le titre est obligatoire');
        }

        if ($activity->getPrice() <= 0) {
            throw new \InvalidArgumentException('Le prix doit être positif');
        }

        if ($activity->getCapacity() <= 0) {
            throw new \InvalidArgumentException('La capacité doit être positive');
        }

        return true;
    }
}
