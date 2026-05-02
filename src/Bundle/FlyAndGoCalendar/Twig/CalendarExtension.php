<?php

namespace App\Bundle\FlyAndGoCalendar\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CalendarExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('fullcalendar_css', [], [
                'is_safe' => ['html'],
                'tag' => 'stylesheets',
            ]),
            new TwigFunction('fullcalendar_js', [], [
                'is_safe' => ['html'],
                'tag' => 'javascripts',
            ]),
        ];
    }

    public function getName(): string
    {
        return 'flyandgo_calendar_extension';
    }
}
