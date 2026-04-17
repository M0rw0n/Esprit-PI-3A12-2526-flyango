<?php

namespace App\Service;

class CircuitAiPlanner
{
    public function generate(array $data): array
    {
        $destination = trim((string) ($data['destination'] ?? 'Destination'));
        $depart = trim((string) ($data['depart'] ?? 'Tunis'));
        $style = trim((string) ($data['style'] ?? 'Découverte'));
        $budget = trim((string) ($data['budget'] ?? 'Moyen'));
        $participants = max(1, (int) ($data['participants'] ?? 2));
        $jours = max(1, (int) ($data['jours'] ?? 3));
        $dateDepart = (string) ($data['date_depart'] ?? date('Y-m-d'));
        $dateRetour = (string) ($data['date_retour'] ?? date('Y-m-d', strtotime('+' . max(1, $jours - 1) . ' days')));

        $difficulty = match (mb_strtolower($style)) {
            'aventure' => 'Difficile',
            'romantique', 'détente', 'detente' => 'Facile',
            'famille' => 'Facile',
            default => 'Modéré',
        };

        $budgetMultiplier = match (mb_strtolower($budget)) {
            'economique', 'économique', 'low' => 120,
            'premium', 'haut', 'luxe' => 280,
            default => 180,
        };

        $price = round(($budgetMultiplier * $jours) + ($participants * 35), 3);
        $title = sprintf('%s — %s %d jours', ucfirst($style), $destination, $jours);

        $highlights = match (mb_strtolower($style)) {
            'aventure' => ['roadbook actif', 'expériences outdoor', 'spots photo au lever du soleil'],
            'romantique' => ['hôtel charme', 'coucher de soleil', 'dîner spécial couple'],
            'famille' => ['rythme doux', 'activités adaptées enfants', 'temps libre équilibré'],
            'détente', 'detente' => ['spa & bien-être', 'transferts optimisés', 'temps libre premium'],
            default => ['visites incontournables', 'temps libre', 'bon équilibre entre culture et détente'],
        };

        $dailyPlan = [];
        for ($day = 1; $day <= $jours; $day++) {
            if ($day === 1) {
                $dailyPlan[] = "Jour {$day} — Départ de {$depart}, arrivée à {$destination}, installation et briefing personnalisé.";
            } elseif ($day === $jours) {
                $dailyPlan[] = "Jour {$day} — Dernières découvertes à {$destination}, shopping libre puis retour.";
            } else {
                $dailyPlan[] = "Jour {$day} — Programme {$style} à {$destination} : visite guidée, pause locale et activité recommandée par l'IA.";
            }
        }

        $description = "Circuit sur mesure généré automatiquement pour {$destination}.\n"
            . "Style choisi : {$style}. Budget : {$budget}. Voyageurs : {$participants}.\n"
            . "Période suggérée : du {$dateDepart} au {$dateRetour}.\n\n"
            . "Points forts :\n- " . implode("\n- ", $highlights) . "\n\n"
            . "Programme proposé :\n- " . implode("\n- ", $dailyPlan) . "\n\n"
            . "Conseil IA : prévoyez une marge de temps pour les transferts, gardez une journée flexible et confirmez les disponibilités 48h avant le départ.";

        return [
            'titre' => $title,
            'description' => $description,
            'duree' => $jours . ' jours',
            'prix' => $price,
            'difficulte' => $difficulty,
            'depart' => $depart,
            'destination' => $destination,
            'places' => max(2, $participants),
            'generated_context' => json_encode([
                'destination' => $destination,
                'depart' => $depart,
                'style' => $style,
                'budget' => $budget,
                'participants' => $participants,
                'jours' => $jours,
                'date_depart' => $dateDepart,
                'date_retour' => $dateRetour,
            ], JSON_UNESCAPED_UNICODE),
        ];
    }
}
