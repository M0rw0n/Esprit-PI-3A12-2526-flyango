<?php

namespace App\Service;

use App\Entity\Hebergement;
use App\Entity\Avis;
use App\Entity\Reservation;
use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class MercureNotificationService
{
    private ?HubInterface $hub = null;

    public function __construct(?HubInterface $hub = null)
    {
        $this->hub = $hub;
    }

    public function notifyHebergementCreated(Hebergement $hebergement): void
    {
        $this->publish('hebergement', 'hebergement_created', [
            'type' => 'hebergement_created',
            'id' => $hebergement->getId(),
            'nom' => $hebergement->getNom(),
            'ville' => $hebergement->getVille(),
            'type' => $hebergement->getType(),
            'prix_par_nuit' => $hebergement->getPrixParNuit(),
            'disponible' => $hebergement->isDisponible(),
            'message' => 'Nouvel hébergement ajouté: ' . $hebergement->getNom(),
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    public function notifyHebergementUpdated(Hebergement $hebergement): void
    {
        $this->publish('hebergement', 'hebergement_updated', [
            'type' => 'hebergement_updated',
            'id' => $hebergement->getId(),
            'nom' => $hebergement->getNom(),
            'ville' => $hebergement->getVille(),
            'type' => $hebergement->getType(),
            'prix_par_nuit' => $hebergement->getPrixParNuit(),
            'disponible' => $hebergement->isDisponible(),
            'message' => 'Hébergement modifié: ' . $hebergement->getNom(),
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    public function notifyHebergementDeleted(int $hebergementId, string $nom): void
    {
        $this->publish('hebergement', 'hebergement_deleted', [
            'type' => 'hebergement_deleted',
            'id' => $hebergementId,
            'nom' => $nom,
            'message' => 'Hébergement supprimé: ' . $nom,
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    public function notifyHebergementUnavailable(Hebergement $hebergement): void
    {
        $this->publish('hebergement', 'hebergement_unavailable', [
            'type' => 'hebergement_unavailable',
            'id' => $hebergement->getId(),
            'nom' => $hebergement->getNom(),
            'ville' => $hebergement->getVille(),
            'message' => 'Hébergement devenu indisponible: ' . $hebergement->getNom(),
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    public function notifyAvisCreated(Avis $avis): void
    {
        $hebergement = $avis->getHebergement();
        $user = $avis->getUser();
        
        $this->publish('hebergement_avis_' . ($hebergement?->getId() ?? 0), 'avis_created', [
            'type' => 'avis_created',
            'id' => $avis->getId(),
            'hebergement_id' => $hebergement?->getId(),
            'hebergement_nom' => $hebergement?->getNom() ?? 'Inconnu',
            'user_nom' => $user?->getNom() ?? $user?->getEmail() ?? 'Anonyme',
            'note' => $avis->getNote(),
            'commentaire' => $avis->getCommentaire() ? mb_substr($avis->getCommentaire(), 0, 100) : null,
            'message' => 'Nouvel avis sur: ' . ($hebergement?->getNom() ?? 'Inconnu'),
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    public function notifyReservationConfirmed(Reservation $reservation): void
    {
        $hebergement = $reservation->getHebergement();
        $user = $reservation->getUser();
        
        $this->publish('hebergement_reservation_' . ($hebergement?->getId() ?? 0), 'reservation_confirmed', [
            'type' => 'reservation_confirmed',
            'id' => $reservation->getId(),
            'hebergement_id' => $hebergement?->getId(),
            'hebergement_nom' => $hebergement?->getNom() ?? 'Inconnu',
            'user_nom' => $user?->getNom() ?? $user?->getEmail() ?? 'Anonyme',
            'date_arrivee' => $reservation->getDateDebut()?->format('Y-m-d'),
            'date_depart' => $reservation->getDateFin()?->format('Y-m-d'),
            'nbre_personnes' => $reservation->getNombrePersonnes(),
            'prix_total' => $reservation->getMontantTotal(),
            'message' => 'Réservation confirmée pour: ' . ($hebergement?->getNom() ?? 'Inconnu'),
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);

        if ($user) {
            $this->publish('user_' . $user->getId(), 'reservation_confirmed', [
                'type' => 'reservation_confirmed',
                'id' => $reservation->getId(),
                'hebergement_nom' => $hebergement?->getNom() ?? 'Inconnu',
                'message' => 'Votre réservation a été confirmée!',
                'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
            ]);
        }
    }

    public function notifyReservationCancelled(Reservation $reservation): void
    {
        $hebergement = $reservation->getHebergement();
        $user = $reservation->getUser();
        
        $this->publish('hebergement_reservation_' . ($hebergement?->getId() ?? 0), 'reservation_cancelled', [
            'type' => 'reservation_cancelled',
            'id' => $reservation->getId(),
            'hebergement_id' => $hebergement?->getId(),
            'hebergement_nom' => $hebergement?->getNom() ?? 'Inconnu',
            'user_nom' => $user?->getNom() ?? $user?->getEmail() ?? 'Anonyme',
            'message' => 'Réservation annulée pour: ' . ($hebergement?->getNom() ?? 'Inconnu'),
            'timestamp' => (new \DateTime())->format('Y-m-d H:i:s'),
        ]);
    }

    private function publish(string $topic, string $event, array $data): void
    {
        if ($this->hub === null) {
            return;
        }

        try {
            $this->hub->publish(new Update(
                'https://flyandgo.com/' . $topic,
                json_encode(['event' => $event, 'data' => $data])
            ));
        } catch (\Exception $e) {
            error_log('Mercure notification failed: ' . $e->getMessage());
        }
    }

    public function isEnabled(): bool
    {
        return $this->hub !== null;
    }
}