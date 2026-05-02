<?php

namespace App\Service;

use App\Entity\Hebergement;
use App\Entity\Avis;
use App\Entity\Reservation;

class NotificationService
{
    private static string $file = '';
    
    private static function getFile(): string
    {
        if (!self::$file) {
            self::$file = dirname(__DIR__, 2) . '/var/notifications.json';
            $dir = dirname(self::$file);
            if (!is_dir($dir)) @mkdir($dir, 0777, true);
        }
        return self::$file;
    }

    private static function load(): array
    {
        $file = self::getFile();
        if (file_exists($file) && is_readable($file)) {
            $data = json_decode(file_get_contents($file), true);
            return $data ?: [];
        }
        return [];
    }
    
    private static function save(array $data): void
    {
        @file_put_contents(self::getFile(), json_encode($data));
    }

    public function notifyHebergementCreated(Hebergement $h): void
    {
        $this->notify('hebergement_created', [
            'type' => 'hebergement_created',
            'id' => $h->getId(),
            'nom' => $h->getNom(),
            'ville' => $h->getVille(),
            'type' => $h->getType(),
            'prix' => $h->getPrixParNuit(),
            'message' => 'Nouvel hébergement: ' . $h->getNom(),
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    public function notifyHebergementUpdated(Hebergement $h): void
    {
        $this->notify('hebergement_updated', [
            'type' => 'hebergement_updated',
            'id' => $h->getId(),
            'nom' => $h->getNom(),
            'message' => 'Hébergement modifié: ' . $h->getNom(),
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    public function notifyHebergementDeleted(int $id, string $nom): void
    {
        $this->notify('hebergement_deleted', [
            'type' => 'hebergement_deleted',
            'id' => $id,
            'nom' => $nom,
            'message' => 'Hébergement supprimé: ' . $nom,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    public function notifyHebergementUnavailable(Hebergement $h): void
    {
        $this->notify('hebergement_unavailable', [
            'type' => 'hebergement_unavailable',
            'id' => $h->getId(),
            'nom' => $h->getNom(),
            'message' => 'Hébergement indisponible: ' . $h->getNom(),
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    public function notifyAvisCreated(Avis $avis): void
    {
        $heb = $avis->getHebergement();
        $this->notify('avis_created', [
            'type' => 'avis_created',
            'hebergement_id' => $heb?->getId(),
            'hebergement_nom' => $heb?->getNom() ?? 'Inconnu',
            'note' => $avis->getNote(),
            'message' => 'Nouvel avis sur: ' . ($heb?->getNom() ?? 'Inconnu'),
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    public function notifyReservationConfirmed(Reservation $r): void
    {
        $heb = $r->getHebergement();
        $this->notify('reservation_confirmed', [
            'type' => 'reservation_confirmed',
            'hebergement_id' => $heb?->getId(),
            'hebergement_nom' => $heb?->getNom(),
            'message' => 'Réservation confirmée: ' . ($heb?->getNom() ?? 'Inconnu'),
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function notify(string $event, array $data): void
    {
        $all = self::load();
        $all[] = [
            'id' => count($all) + 1,
            'event' => $event,
            'data' => $data,
        ];
        if (count($all) > 100) {
            $all = array_slice($all, -50);
        }
        self::save($all);
    }

    public static function getLastId(): int
    {
        $all = self::load();
        return count($all);
    }

    public static function getNotifications(): array
    {
        return self::load();
    }
}