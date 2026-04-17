<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Circuit;
use App\Entity\CircuitAvis;
use App\Entity\ReservationCircuit;
use App\Entity\User;
use App\Repository\CircuitRepository;
use App\Service\CircuitAiPlanner;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/circuits')]
class CircuitController extends AbstractController
{
    #[Route('', name: 'circuit_index', methods: ['GET'])]
    public function index(Request $request, CircuitRepository $repo): Response
    {
        $q = $request->query->get('q');
        $difficulte = $request->query->get('difficulte');
        $sort = $request->query->get('sort', 'recent');
        $circuits = $repo->searchVisible($q, $difficulte, $sort, $this->getUser() instanceof User ? $this->getUser() : null);

        return $this->render('circuit/index.html.twig', [
            'circuits' => $circuits,
            'q' => $q,
            'difficulte' => $difficulte,
            'sort' => $sort,
        ]);
    }

    #[Route('/personnalise', name: 'circuit_ai_create', methods: ['GET', 'POST'])]
    public function createAi(Request $request, CircuitAiPlanner $planner, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($request->isMethod('POST')) {
            /** @var User $user */
            $user = $this->getUser();
            $payload = [
                'destination' => $request->request->get('destination'),
                'depart' => $request->request->get('depart', 'Tunis'),
                'style' => $request->request->get('style', 'Découverte'),
                'budget' => $request->request->get('budget', 'Moyen'),
                'participants' => $request->request->get('participants', 2),
                'jours' => $request->request->get('jours', 3),
                'date_depart' => $request->request->get('date_depart'),
                'date_retour' => $request->request->get('date_retour'),
            ];

            $result = $planner->generate($payload);

            $circuit = (new Circuit())
                ->setTitre($result['titre'] ?? '')
                ->setDescription($result['description'] ?? null)
                ->setDuree((int) ($result['duree'] ?? 1))
                ->setPrix((float) ($result['prix'] ?? 0))
                ->setDifficulte($result['difficulte'] ?? 'Moyen')
                ->setDepart($result['depart'] ?? 'Tunis')
                ->setDestination($result['destination'] ?? '')
                ->setPlacesDisponibles((int) ($result['places'] ?? 10))
                ->setActif(true)
                ->setIsCustom(true)
                ->setIsAiGenerated(true)
                ->setSourceType('custom')
                ->setGeneratedContext($result['generated_context'])
                ->setCreator($user);

            $em->persist($circuit);
            $em->flush();

            $this->addFlash('success', '🧠 Circuit sur mesure généré avec succès.');
            return $this->redirectToRoute('circuit_show', ['id' => $circuit->getId()]);
        }

        return $this->render('circuit/create_ai.html.twig');
    }

    #[Route('/{id}', name: 'circuit_show', methods: ['GET'], requirements: ['id' => '\\d+'])]
    public function show(int $id, CircuitRepository $repo): Response
    {
        $c = $repo->find($id);
        if (!$c) {
            throw $this->createNotFoundException();
        }

        if ($c->getSourceType() !== 'admin') {
            $user = $this->getUser();
            if (!$user instanceof User || $c->getCreator()?->getId() !== $user->getId()) {
                throw $this->createNotFoundException();
            }
        }

        $similar = $repo->findSimilar($c, 4);

        return $this->render('circuit/show.html.twig', [
            'circuit' => $c,
            'similarCircuits' => $similar,
        ]);
    }

    #[Route('/{id}/reserver', name: 'circuit_reserver', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function reserver(int $id, Request $request, CircuitRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $c = $repo->find($id);
        if (!$c) {
            throw $this->createNotFoundException();
        }

        /** @var User $user */
        $user = $this->getUser();
        $nbPersonnes = (int) $request->request->get('nb_personnes', 1);
        $montant = $nbPersonnes * $c->getPrix();

        $res = new ReservationCircuit();
        $res->setCircuit($c)
            ->setUser($user)
            ->setNomClient($user->getFullName())
            ->setEmailClient($user->getEmail())
            ->setTelephone($user->getTelephone())
            ->setDateReservation(new \DateTimeImmutable($request->request->get('date_depart', 'now')))
            ->setNbPersonnes($nbPersonnes)
            ->setMontantTotal($montant)
            ->setStatut('EN_ATTENTE');

        $em->persist($res);
        $em->flush();

        $this->addFlash('success', '🗺️ Réservation circuit ajoutée à votre espace.');
        return $this->redirectToRoute('user_reservations');
    }

    #[Route('/{id}/avis', name: 'circuit_review', methods: ['POST'], requirements: ['id' => '\\d+'])]
    public function addReview(int $id, Request $request, CircuitRepository $repo, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $circuit = $repo->find($id);
        if (!$circuit) {
            throw $this->createNotFoundException();
        }

        /** @var User $user */
        $user = $this->getUser();
        $review = $em->getRepository(CircuitAvis::class)->findOneBy(['circuit' => $circuit, 'user' => $user]) ?? new CircuitAvis();

        $review->setCircuit($circuit)
            ->setUser($user)
            ->setAuthor($user->getFullName())
            ->setRating((int) $request->request->get('rating', 5))
            ->setComment((string) $request->request->get('comment', ''));

        if ($review->getId() === null) {
            $em->persist($review);
        }
        $em->flush();

        $this->addFlash('success', '⭐ Votre avis circuit a bien été enregistré.');
        return $this->redirectToRoute('user_avis');
    }
}
