<?php

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('time_ago', [$this, 'timeAgo']),
        ];
    }

    public function getFunctions(): array
    {
        return [];
    }

    public function timeAgo(\DateTimeInterface $date): string
    {
        $now = new \DateTime();
        $diff = $now->getTimestamp() - $date->getTimestamp();

        if ($diff < 60) {
            return 'à l\'instant';
        }

        if ($diff < 3600) {
            $minutes = floor($diff / 60);
            return "il y a {$minutes} min";
        }

        if ($diff < 86400) {
            $hours = floor($diff / 3600);
            return "il y a {$hours}h";
        }

        if ($diff < 604800) {
            $days = floor($diff / 86400);
            return "il y a {$days}j";
        }

        if ($diff < 2592000) {
            $weeks = floor($diff / 604800);
            return "il y a {$weeks}sem";
        }

        if ($diff < 31536000) {
            $months = floor($diff / 2592000);
            return "il y a {$months} mois";
        }

        $years = floor($diff / 31536000);
        return "il y a {$years} an" . ($years > 1 ? 's' : '');
    }
}
