<?php

namespace App\Bundle\FlyAndGoCalendar\Service;

class CalendarService
{
    private string $version = '6.1.10';
    
    public function getCssUrl(): string
    {
        return "https://cdn.jsdelivr.net/npm/fullcalendar@{$this->version}/index.global.min.css";
    }
    
    public function getJsUrl(): string
    {
        return "https://cdn.jsdelivr.net/npm/fullcalendar@{$this->version}/index.global.min.js";
    }
    
    public function getLocalesUrl(): string
    {
        return "https://cdn.jsdelivr.net/npm/fullcalendar@{$this->version}/locales-all.global.min.js";
    }
    
    public function getVersion(): string
    {
        return $this->version;
    }
    
    public function getDefaultOptions(): array
    {
        return [
            'headerToolbar' => [
                'left' => 'prev,next today',
                'center' => 'title',
                'right' => 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
            ],
            'initialView' => 'dayGridMonth',
            'navLinks' => true,
            'editable' => true,
            'dayMaxEvents' => true,
            'locale' => 'fr',
            'buttonText' => [
                'today' => 'Aujourd\'hui',
                'month' => 'Mois',
                'week' => 'Semaine',
                'day' => 'Jour',
                'list' => 'Liste'
            ],
            'eventTimeFormat' => [
                'hour' => '2-digit',
                'minute' => '2-digit',
                'meridiem' => 'short'
            ],
            'firstDay' => 1,
            'weekends' => true,
            'weekNumberTitle' => 'Sm',
            'allDayText' => 'Toute la journée',
            'moreLinkText' => '+%d autres',
            'noEventsText' => 'Aucun événement à afficher',
        ];
    }
}
