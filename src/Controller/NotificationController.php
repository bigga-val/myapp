<?php

namespace App\Controller;

use App\Repository\ClasseRepository;
use App\Repository\EmpruntLivreRepository;
use App\Repository\PaiementFraisRepository;
use App\Repository\PresenceRepository;
use App\Service\AnneeAcademiqueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class NotificationController extends AbstractController
{
    #[Route('/api/notifications', name: 'api_notifications', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        EmpruntLivreRepository  $empruntRepo,
        PaiementFraisRepository $paiementRepo,
        ClasseRepository        $classeRepo,
        PresenceRepository      $presenceRepo,
        AnneeAcademiqueService  $anneeService,
    ): JsonResponse {
        $notifications = [];
        $annee = $anneeService->getCurrent();

        // ── 1. Emprunts en retard ──────────────────────────────────────────
        $retards = $empruntRepo->findEnRetard();
        if (count($retards) > 0) {
            $notifications[] = [
                'id'      => 'retard-livres',
                'type'    => 'danger',
                'icon'    => 'book',
                'title'   => count($retards) . ' emprunt(s) en retard',
                'message' => 'Des livres n\'ont pas été retournés à temps.',
                'url'     => '/bibliotheque/emprunts?statut=en_retard',
                'time'    => 'Aujourd\'hui',
            ];
        }

        // ── 2. Frais scolaires en attente (mois courant) ──────────────────
        if ($annee) {
            $enAttente = $paiementRepo->countEnAttente($annee);
            if ($enAttente > 0) {
                $notifications[] = [
                    'id'      => 'frais-attente',
                    'type'    => 'warning',
                    'icon'    => 'money',
                    'title'   => $enAttente . ' frais en attente',
                    'message' => 'Des frais scolaires n\'ont pas encore été réglés.',
                    'url'     => '/frais-scolaires/',
                    'time'    => date('F Y'),
                ];
            }
        }

        // ── 3. Classes sans présences saisies aujourd'hui ─────────────────
        if ($annee) {
            $today      = new \DateTime('today');
            $allClasses = $classeRepo->findBy(['anneeAcademique' => $annee]);
            $sansPresence = [];

            foreach ($allClasses as $classe) {
                $presences = $presenceRepo->findByClasseAndDate($classe, $today);
                if (count($presences) === 0) {
                    $sansPresence[] = $classe->getNom();
                }
            }

            if (count($sansPresence) > 0) {
                $liste = implode(', ', array_slice($sansPresence, 0, 3));
                $extra = count($sansPresence) > 3 ? ' +' . (count($sansPresence) - 3) . ' autres' : '';
                $notifications[] = [
                    'id'      => 'presences-manquantes',
                    'type'    => 'info',
                    'icon'    => 'clipboard',
                    'title'   => count($sansPresence) . ' classe(s) sans présences',
                    'message' => 'Pas encore saisies aujourd\'hui : ' . $liste . $extra,
                    'url'     => '/presence/',
                    'time'    => 'Aujourd\'hui',
                ];
            }
        }

        return $this->json($notifications);
    }
}
