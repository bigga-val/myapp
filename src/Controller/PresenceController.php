<?php

namespace App\Controller;

use App\Entity\Presence;
use App\Repository\ClasseRepository;
use App\Repository\EleveRepository;
use App\Repository\InscriptionRepository;
use App\Repository\PresenceRepository;
use App\Service\AnneeAcademiqueService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/presence')]
class PresenceController extends AbstractController
{
    #[Route('/', name: 'app_presence_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        PresenceRepository $presenceRepository,
        ClasseRepository $classeRepository,
    ): Response {
        $classeId = $request->query->get('classeId');
        $dateParam = $request->query->get('date');
        $date = $dateParam ? new \DateTime($dateParam) : new \DateTime('today');

        $classes = $classeRepository->findBy([], ['createdAt' => 'DESC']);
        $presences = [];
        $classe = null;

        if ($classeId) {
            $classe = $classeRepository->find($classeId);
            if ($classe) {
                $presences = $presenceRepository->findByClasseAndDate($classe, $date);
            }
        }

        return $this->render('presence/index.html.twig', [
            'classes'  => $classes,
            'presences' => $presences,
            'classe'   => $classe,
            'classeId' => $classeId,
            'date'     => $date,
        ]);
    }

    #[Route('/saisie/{classeId}', name: 'app_presence_saisie', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function saisie(
        int $classeId,
        Request $request,
        ClasseRepository $classeRepository,
        InscriptionRepository $inscriptionRepository,
        PresenceRepository $presenceRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $classe = $classeRepository->find($classeId);
        if (!$classe) {
            throw $this->createNotFoundException('Classe introuvable.');
        }

        $dateParam = $request->query->get('date');
        $date = $dateParam ? new \DateTime($dateParam) : new \DateTime('today');

        $inscriptions = $inscriptionRepository->findByClasse($classe);

        // Build a map of existing presences indexed by eleveId
        $existingPresences = $presenceRepository->findByClasseAndDate($classe, $date);
        $presenceMap = [];
        foreach ($existingPresences as $p) {
            $presenceMap[$p->getEleve()->getId()] = $p;
        }

        if ($request->isMethod('POST')) {
            $data = $request->request->all('presences');
            $saisiePar = $this->getUser()->getUserIdentifier();

            foreach ($inscriptions as $inscription) {
                $eleve = $inscription->getEleve();
                $eleveId = $eleve->getId();
                $statutData = $data[$eleveId] ?? null;

                if ($statutData === null) {
                    continue;
                }

                $statut = $statutData['statut'] ?? 'present';
                $motif  = $statutData['motif'] ?? null;

                if (isset($presenceMap[$eleveId])) {
                    $presence = $presenceMap[$eleveId];
                    $presence->setStatut($statut);
                    $presence->setMotif($motif ?: null);
                } else {
                    $presence = new Presence();
                    $presence->setEleve($eleve);
                    $presence->setClasse($classe);
                    $presence->setDate($date);
                    $presence->setStatut($statut);
                    $presence->setMotif($motif ?: null);
                    $presence->setSaisiePar($saisiePar);
                    $entityManager->persist($presence);
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Présences enregistrées avec succès.');

            return $this->redirectToRoute('app_presence_index', [
                'classeId' => $classeId,
                'date'     => $date->format('Y-m-d'),
            ]);
        }

        return $this->render('presence/saisie.html.twig', [
            'classe'       => $classe,
            'date'         => $date,
            'inscriptions' => $inscriptions,
            'presenceMap'  => $presenceMap,
        ]);
    }

    #[Route('/{eleveId}/rapport', name: 'app_presence_rapport', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function rapport(
        int $eleveId,
        Request $request,
        EleveRepository $eleveRepository,
        PresenceRepository $presenceRepository,
        AnneeAcademiqueService $anneeService,
    ): Response {
        $eleve = $eleveRepository->find($eleveId);
        if (!$eleve) {
            throw $this->createNotFoundException('Élève introuvable.');
        }

        $debutParam = $request->query->get('debut');
        $finParam   = $request->query->get('fin');

        $annee = $anneeService->getCurrent();

        $debut = $debutParam ? new \DateTime($debutParam) : ($annee ? $annee->getDateDebut() : new \DateTime('first day of january this year'));
        $fin   = $finParam   ? new \DateTime($finParam)   : new \DateTime('today');

        $presences = $presenceRepository->findByEleveAndPeriode($eleve, $debut, $fin);

        $stats = [
            'total'    => count($presences),
            'present'  => 0,
            'absent'   => 0,
            'retard'   => 0,
            'justifie' => 0,
        ];
        foreach ($presences as $p) {
            match ($p->getStatut()) {
                'present'  => $stats['present']++,
                'absent'   => $stats['absent']++,
                'retard'   => $stats['retard']++,
                'justifié' => $stats['justifie']++,
                default    => null,
            };
        }

        return $this->render('presence/rapport.html.twig', [
            'eleve'    => $eleve,
            'presences' => $presences,
            'stats'    => $stats,
            'debut'    => $debut,
            'fin'      => $fin,
        ]);
    }

    #[Route('/{id}', name: 'app_presence_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function delete(
        Request $request,
        Presence $presence,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $presence->getId(), $request->request->get('_token'))) {
            $entityManager->remove($presence);
            $entityManager->flush();
            $this->addFlash('success', 'Enregistrement supprimé.');
        }

        return $this->redirectToRoute('app_presence_index', [], Response::HTTP_SEE_OTHER);
    }
}
