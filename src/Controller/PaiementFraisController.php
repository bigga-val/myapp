<?php

namespace App\Controller;

use App\Entity\PaiementFrais;
use App\Form\PaiementFraisType;
use App\Repository\EleveRepository;
use App\Repository\FraisScolaireRepository;
use App\Repository\InscriptionRepository;
use App\Repository\PaiementFraisRepository;
use App\Service\AnneeAcademiqueService;
use App\Service\MatriculeService;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/paiements', name: 'app_paiement_frais_')]
class PaiementFraisController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        PaiementFraisRepository $paiementRepo,
        EleveRepository $eleveRepo,
    ): Response {
        $eleveId = $request->query->get('eleveId');
        $mois    = $request->query->get('mois'); // format: YYYY-MM

        $eleve = $eleveId ? $eleveRepo->find($eleveId) : null;

        if ($mois) {
            [$anneeStr, $moisStr] = explode('-', $mois);
            $paiements = $paiementRepo->findByMois((int) $anneeStr, (int) $moisStr);
            $totalMois = $paiementRepo->sumPercusMois((int) $anneeStr, (int) $moisStr);
            if ($eleve) {
                $paiements = array_filter($paiements, fn ($p) => $p->getEleve()->getId() === $eleve->getId());
                $paiements = array_values($paiements);
            }
        } elseif ($eleve) {
            $paiements = $paiementRepo->findByEleve($eleve);
            $totalMois = null;
        } else {
            $paiements = $paiementRepo->findBy([], ['datePaiement' => 'DESC']);
            $totalMois = null;
        }

        $eleves = $eleveRepo->findBy([], ['nom' => 'ASC']);

        return $this->render('paiement_frais/index.html.twig', [
            'paiements'  => $paiements,
            'eleves'     => $eleves,
            'eleveActif' => $eleve,
            'moisActif'  => $mois,
            'totalMois'  => $totalMois,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function new(
        Request $request,
        EntityManagerInterface $em,
        PaiementFraisRepository $paiementRepo,
        InscriptionRepository $inscriptionRepo,
        AnneeAcademiqueService $anneeService,
        MatriculeService $matriculeService,
    ): Response {
        // Map eleveId → nom de classe pour l'année courante
        $classesParEleve = [];
        $annee = $anneeService->getCurrent();
        if ($annee) {
            foreach ($inscriptionRepo->findBy(['anneeAcademique' => $annee]) as $inscription) {
                $classesParEleve[$inscription->getEleve()->getId()] = $inscription->getClasse()->getNom();
            }
        }

        $paiement = new PaiementFrais();
        $form     = $this->createForm(PaiementFraisType::class, $paiement, [
            'classes_par_eleve' => $classesParEleve,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $nextSeq = $paiementRepo->count([]) + 1;
            $paiement->setNumeroRecu($matriculeService->generateNumeroRecu($nextSeq));
            $paiement->setEnregistrePar($this->getUser()->getUserIdentifier());

            $em->persist($paiement);
            $em->flush();

            $this->addFlash('success', 'Paiement enregistré. Reçu : ' . $paiement->getNumeroRecu());

            return $this->redirectToRoute('app_paiement_frais_show', ['id' => $paiement->getId()]);
        }

        return $this->render('paiement_frais/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Paiement en lot depuis la fiche élève (plusieurs frais en un seul reçu).
     */
    #[Route('/batch', name: 'batch', methods: ['POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function batch(
        Request                 $request,
        EntityManagerInterface  $em,
        EleveRepository         $eleveRepo,
        FraisScolaireRepository $fraisRepo,
        PaiementFraisRepository $paiementRepo,
        MatriculeService        $matriculeService,
    ): Response {
        $eleveId   = $request->request->get('eleveId');
        $fraisIds  = $request->request->all('fraisIds');   // tableau de IDs
        $dateRaw   = $request->request->get('datePaiement');
        $mode      = $request->request->get('modePaiement', 'especes');
        $obs       = $request->request->get('observations');

        $eleve = $eleveRepo->find($eleveId);
        if (!$eleve || empty($fraisIds)) {
            $this->addFlash('danger', 'Données invalides.');
            return $this->redirectToRoute('app_eleve_show', ['id' => $eleveId]);
        }

        $date = $dateRaw ? new \DateTime($dateRaw) : new \DateTime('today');
        $paiementIds = [];

        foreach ($fraisIds as $fraisId) {
            $frais = $fraisRepo->find($fraisId);
            if (!$frais) continue;

            $nextSeq  = $paiementRepo->count([]) + count($paiementIds) + 1;
            $paiement = new PaiementFrais();
            $paiement->setEleve($eleve);
            $paiement->setFraisScolaire($frais);
            $paiement->setMontantPaye($frais->getMontant());
            $paiement->setDatePaiement($date);
            $paiement->setModePaiement($mode);
            $paiement->setNumeroRecu($matriculeService->generateNumeroRecu($nextSeq));
            $paiement->setEnregistrePar($this->getUser()->getUserIdentifier());
            $paiement->setObservations($obs);

            $em->persist($paiement);
            $em->flush();   // flush immédiatement pour avoir l'ID

            $paiementIds[] = $paiement->getId();
        }

        if (empty($paiementIds)) {
            $this->addFlash('warning', 'Aucun paiement enregistré.');
            return $this->redirectToRoute('app_eleve_show', ['id' => $eleveId]);
        }

        $this->addFlash('success', count($paiementIds) . ' paiement(s) enregistré(s).');

        // Rediriger vers le reçu groupé
        return $this->redirectToRoute('app_paiement_frais_recu_multiple', [
            'ids' => implode(',', $paiementIds),
        ]);
    }

    /**
     * Reçu PDF groupé pour plusieurs paiements.
     */
    #[Route('/recu-multiple', name: 'recu_multiple', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function recuMultiple(Request $request, PaiementFraisRepository $paiementRepo): Response
    {
        $ids       = array_filter(array_map('intval', $request->query->all('ids')));
        $paiements = array_filter(array_map(fn($id) => $paiementRepo->find($id), $ids));

        if (empty($paiements)) {
            throw $this->createNotFoundException('Paiements introuvables.');
        }

        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $html   = $this->renderView('paiement_frais/recu_multiple_pdf.html.twig', [
            'paiements' => array_values($paiements),
            'eleve'     => reset($paiements)->getEleve(),
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A5', 'portrait');
        $dompdf->render();

        $firstRecu = reset($paiements)->getNumeroRecu();

        return new Response($dompdf->output(), 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="recu-groupe-' . $firstRecu . '.pdf"',
        ]);
    }

    /**
     * API : retourne les frais éligibles pour un élève donné.
     * Filtre : liés à la classe de l'élève (année courante) + non encore payés.
     */
    #[Route('/api/frais-eligibles', name: 'api_frais_eligibles', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function fraisEligibles(
        Request                 $request,
        EleveRepository         $eleveRepo,
        InscriptionRepository   $inscriptionRepo,
        FraisScolaireRepository $fraisRepo,
        PaiementFraisRepository $paiementRepo,
        AnneeAcademiqueService  $anneeService,
    ): JsonResponse {
        $eleveId = (int) $request->query->get('eleveId');
        if (!$eleveId) {
            return $this->json([]);
        }

        $eleve = $eleveRepo->find($eleveId);
        if (!$eleve) {
            return $this->json([]);
        }

        $annee = $anneeService->getCurrent();
        if (!$annee) {
            return $this->json([]);
        }

        // Classe de l'élève pour l'année courante
        $inscription = $inscriptionRepo->findOneBy(['eleve' => $eleve, 'anneeAcademique' => $annee]);
        $classe = $inscription?->getClasse();

        // Frais applicables à cette classe (globaux + spécifiques à la classe)
        $fraisApplicables = $fraisRepo->findByClasseAndAnnee($classe, $annee);

        // IDs des frais déjà payés par cet élève
        $dejaPayesIds = array_unique(array_map(
            fn($p) => $p->getFraisScolaire()->getId(),
            $paiementRepo->findBy(['eleve' => $eleve])
        ));

        // Exclure les frais déjà payés
        $eligibles = array_filter($fraisApplicables, fn($f) => !in_array($f->getId(), $dejaPayesIds));

        return $this->json(array_values(array_map(fn($f) => [
            'id'      => $f->getId(),
            'libelle' => $f->getLibelle(),
            'montant' => $f->getMontant(),
        ], $eligibles)));
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(PaiementFrais $paiement): Response
    {
        return $this->render('paiement_frais/show.html.twig', [
            'paiement' => $paiement,
        ]);
    }

    #[Route('/{id}/pdf', name: 'pdf', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function pdf(PaiementFrais $paiement): Response
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);

        $dompdf = new Dompdf($options);
        $html   = $this->renderView('paiement_frais/recu_pdf.html.twig', [
            'paiement' => $paiement,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A5', 'portrait');
        $dompdf->render();
        $output = $dompdf->output();

        return new Response($output, 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="recu-' . $paiement->getNumeroRecu() . '.pdf"',
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function delete(Request $request, PaiementFrais $paiement, EntityManagerInterface $em): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $paiement->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_paiement_frais_index');
        }

        $em->remove($paiement);
        $em->flush();

        $this->addFlash('success', 'Paiement supprimé.');

        return $this->redirectToRoute('app_paiement_frais_index');
    }
}
