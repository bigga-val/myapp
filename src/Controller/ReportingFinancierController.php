<?php

namespace App\Controller;

use App\Entity\FraisScolaire;
use App\Entity\PaiementFrais;
use App\Repository\ClasseRepository;
use App\Repository\CreditRepository;
use App\Repository\DebitRepository;
use App\Repository\EleveRepository;
use App\Repository\FraisScolaireRepository;
use App\Repository\InscriptionRepository;
use App\Repository\PaieEmployeRepository;
use App\Repository\PaiementFraisRepository;
use App\Service\AnneeAcademiqueService;
use App\Service\MatriculeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/reporting/financier')]
#[IsGranted('ROLE_ADMIN')]
class ReportingFinancierController extends AbstractController
{
    // ── Tableau de bord financier ────────────────────────────────────────────

    #[Route('', name: 'app_reporting_financier', methods: ['GET'])]
    public function index(
        Request                 $request,
        PaiementFraisRepository $paiementFraisRepo,
        CreditRepository        $creditRepo,
        DebitRepository         $debitRepo,
        PaieEmployeRepository   $paieEmployeRepo,
        AnneeAcademiqueService  $anneeService,
    ): Response {
        $annee             = (int) ($request->query->get('annee', date('Y')));
        $anneesDisponibles = range((int) date('Y'), max((int) date('Y') - 4, 2020));

        // Filtre mois : paramètres indépendants du filtre annuel
        $moisCourant   = (int) ($request->query->get('mois',      date('n')));
        $anneeMois     = (int) ($request->query->get('anneeMois', date('Y')));
        $moisCourant   = max(1, min(12, $moisCourant)); // garde-fou 1-12

        $fraisPercusMois = $paiementFraisRepo->sumPercusMois($anneeMois, $moisCourant);
        $totalCredits    = $creditRepo->sumFcMois($anneeMois, $moisCourant);
        $totalDebits     = $debitRepo->sumFcMois($anneeMois, $moisCourant);
        $masseSalariale  = $paieEmployeRepo->masseSalarialeMois($anneeMois, $moisCourant);

        // Solde complet : (Frais scolaires + Recettes) − (Dépenses + Masse salariale)
        $recettesTotalesMois  = $fraisPercusMois + $totalDebits;
        $depensesTotalesMois  = $totalCredits + $masseSalariale;
        $soldeMois            = $recettesTotalesMois - $depensesTotalesMois;

        $totalFraisAnnee    = $paiementFraisRepo->totalPercusAnnee($annee);
        $totalCreditsAnnee  = array_sum($creditRepo->totauxParMoisAnnee($annee));
        $totalDebitsAnnee   = array_sum($debitRepo->totauxParMoisAnnee($annee));
        $totalMasseSalAnnee = array_sum($paieEmployeRepo->masseSalarialeParMois($annee));

        // Solde annuel complet
        $recettesTotalesAnnee  = $totalFraisAnnee + $totalDebitsAnnee;
        $depensesTotalesAnnee  = $totalCreditsAnnee + $totalMasseSalAnnee;
        $soldeAnnee            = $recettesTotalesAnnee - $depensesTotalesAnnee;

        $fraisParMois    = array_values($paiementFraisRepo->percusParMois($annee));
        $creditsParMois  = array_values($creditRepo->totauxParMoisAnnee($annee));
        $debitsParMois   = array_values($debitRepo->totauxParMoisAnnee($annee));
        $masseSalParMois = array_values($paieEmployeRepo->masseSalarialeParMois($annee));

        // Solde cumulé mensuel incluant frais et salaires
        $recettesTotalesParMois = [];
        $depensesTotalesParMois = [];
        $soldeParMois = [];
        $cumul = 0.0;
        for ($m = 0; $m < 12; $m++) {
            $recettesTotalesParMois[] = $fraisParMois[$m] + $debitsParMois[$m];
            $depensesTotalesParMois[] = $creditsParMois[$m] + $masseSalParMois[$m];
            $cumul += $recettesTotalesParMois[$m] - $depensesTotalesParMois[$m];
            $soldeParMois[] = round($cumul, 2);
        }

        $derniersPaiements = $paiementFraisRepo->findBy([], ['datePaiement' => 'DESC'], 10);
        $moisLabels = ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Août', 'Sep', 'Oct', 'Nov', 'Déc'];

        return $this->render('reporting/financier.html.twig', [
            'annee'              => $annee,
            'anneesDisponibles'  => $anneesDisponibles,
            'moisCourant'        => $moisCourant,
            'anneeMois'          => $anneeMois,
            'fraisPercusMois'    => $fraisPercusMois,
            'totalCredits'       => $totalCredits,
            'totalDebits'        => $totalDebits,
            'masseSalariale'     => $masseSalariale,
            'recettesTotalesMois'    => $recettesTotalesMois,
            'depensesTotalesMois'    => $depensesTotalesMois,
            'soldeMois'              => $soldeMois,
            'totalFraisAnnee'        => $totalFraisAnnee,
            'totalCreditsAnnee'      => $totalCreditsAnnee,
            'totalDebitsAnnee'       => $totalDebitsAnnee,
            'totalMasseSalAnnee'     => $totalMasseSalAnnee,
            'recettesTotalesAnnee'   => $recettesTotalesAnnee,
            'depensesTotalesAnnee'   => $depensesTotalesAnnee,
            'soldeAnnee'             => $soldeAnnee,
            'moisLabels'             => $moisLabels,
            'fraisParMois'           => $fraisParMois,
            'creditsParMois'         => $creditsParMois,
            'debitsParMois'          => $debitsParMois,
            'masseSalParMois'        => $masseSalParMois,
            'recettesTotalesParMois' => $recettesTotalesParMois,
            'depensesTotalesParMois' => $depensesTotalesParMois,
            'soldeParMois'           => $soldeParMois,
            'derniersPaiements'  => $derniersPaiements,
        ]);
    }

    // ── Recouvrement des frais (filtre frais + classe) ───────────────────────

    #[Route('/recouvrement', name: 'app_reporting_recouvrement', methods: ['GET'])]
    public function recouvrement(
        Request                  $request,
        PaiementFraisRepository  $paiementFraisRepo,
        InscriptionRepository    $inscriptionRepo,
        FraisScolaireRepository  $fraisScolaireRepo,
        ClasseRepository         $classeRepo,
        AnneeAcademiqueService   $anneeService,
    ): Response {
        $anneeActuelle = $anneeService->getCurrent();

        if (!$anneeActuelle) {
            $this->addFlash('warning', 'Aucune année académique courante définie.');
            return $this->redirectToRoute('app_reporting_financier');
        }

        $tousLesFrais  = $fraisScolaireRepo->findByAnnee($anneeActuelle);
        $toutesClasses = $classeRepo->findBy(['anneeAcademique' => $anneeActuelle], ['nom' => 'ASC']);

        $fraisId  = $request->query->get('fraisId');
        $classeId = $request->query->get('classeId');

        $fraisSelectionne   = $fraisId  ? $fraisScolaireRepo->find($fraisId)  : null;
        $classeSelectionnee = $classeId ? $classeRepo->find($classeId)         : null;

        $ayantPaye  = [];
        $impayes    = [];
        $nbAttendu  = 0;
        $totalPercu = 0.0;

        if ($fraisSelectionne) {
            // Élèves éligibles selon le type de frais
            if (!$fraisSelectionne->getClasses()->isEmpty()) {
                // Frais lié à des classes précises → ignorer le filtre classe de l'URL
                $classeSelectionnee = null; // affichage : "toutes les classes du frais"
                $eligibles = $inscriptionRepo->elevesEligiblesPourFrais($fraisSelectionne, $anneeActuelle);
            } elseif ($classeSelectionnee !== null) {
                // Frais global + classe choisie par l'utilisateur
                $eligibles = $inscriptionRepo->elevesParClasse($classeSelectionnee, $anneeActuelle);
            } else {
                // Frais global, pas de filtre classe → tous les inscrits
                $eligibles = $inscriptionRepo->elevesEligiblesPourFrais($fraisSelectionne, $anneeActuelle);
            }

            // Payeurs de ce frais, filtrés aux éligibles
            $allPayers    = $paiementFraisRepo->elevesAyantPayeFrais($fraisSelectionne);
            $eligibleIds  = array_unique(array_column($eligibles, 'id'));
            $ayantPaye    = array_values(array_filter($allPayers, fn($e) => in_array($e['id'], $eligibleIds, true)));
            $ayantPayeIds = array_unique(array_column($ayantPaye, 'id'));
            $impayes      = array_values(array_filter($eligibles, fn($e) => !in_array($e['id'], $ayantPayeIds, true)));

            $nbAttendu  = count($eligibles);
            $totalPercu = (float) array_sum(array_column($ayantPaye, 'totalPaye'));
        }

        // Map fraisId → [classeId, ...] pour le filtrage JS du select classe
        $fraisClassesMap = [];
        foreach ($tousLesFrais as $f) {
            $ids = [];
            foreach ($f->getClasses() as $c) {
                $ids[] = $c->getId();
            }
            $fraisClassesMap[$f->getId()] = $ids;
        }

        return $this->render('reporting/recouvrement.html.twig', [
            'annee'              => $anneeActuelle,
            'tousLesFrais'       => $tousLesFrais,
            'toutesClasses'      => $toutesClasses,
            'fraisSelectionne'   => $fraisSelectionne,
            'classeSelectionnee' => $classeSelectionnee,
            'ayantPaye'          => $ayantPaye,
            'impayes'            => $impayes,
            'nbAttendu'          => $nbAttendu,
            'totalPercu'         => $totalPercu,
            'fraisId'            => $fraisId,
            'classeId'           => $classeId,
            'fraisClassesMap'    => $fraisClassesMap,
        ]);
    }

    // ── Enregistrement rapide d'un paiement depuis le recouvrement ──────────

    #[Route('/recouvrement/payer', name: 'app_reporting_recouvrement_payer', methods: ['POST'])]
    public function payerRapide(
        Request                 $request,
        EleveRepository         $eleveRepo,
        FraisScolaireRepository $fraisScolaireRepo,
        PaiementFraisRepository $paiementFraisRepo,
        MatriculeService        $matriculeService,
        EntityManagerInterface  $em,
    ): Response {
        $eleveId  = $request->request->get('eleveId');
        $fraisId  = $request->request->get('fraisId');
        $redirect = $request->request->get('redirect', $this->generateUrl('app_reporting_recouvrement'));

        $eleve = $eleveRepo->find($eleveId);
        $frais = $fraisScolaireRepo->find($fraisId);

        if (!$eleve || !$frais) {
            $this->addFlash('danger', 'Élève ou frais introuvable.');
            return $this->redirect($redirect);
        }

        $montant      = (float) $request->request->get('montant', $frais->getMontant());
        $mode         = $request->request->get('modePaiement', 'especes');
        $dateRaw      = $request->request->get('datePaiement');
        $observations = $request->request->get('observations');
        $date         = $dateRaw ? new \DateTime($dateRaw) : new \DateTime('today');

        $nextSeq = $paiementFraisRepo->count([]) + 1;

        $paiement = new PaiementFrais();
        $paiement->setEleve($eleve);
        $paiement->setFraisScolaire($frais);
        $paiement->setMontantPaye($montant);
        $paiement->setDatePaiement($date);
        $paiement->setModePaiement($mode);
        $paiement->setNumeroRecu($matriculeService->generateNumeroRecu($nextSeq));
        $paiement->setEnregistrePar($this->getUser()->getUserIdentifier());
        $paiement->setObservations($observations);

        $em->persist($paiement);
        $em->flush();

        $this->addFlash('success', sprintf(
            'Paiement de %s FC enregistré pour %s. Reçu : %s',
            number_format($montant, 0, '.', ' '),
            $eleve->getNomComplet(),
            $paiement->getNumeroRecu()
        ));

        return $this->redirect($redirect);
    }

    // ── Détail élève par élève pour un frais donné ───────────────────────────

    #[Route('/frais/{id}', name: 'app_reporting_financier_frais', methods: ['GET'])]
    public function detailFrais(
        FraisScolaire           $frais,
        PaiementFraisRepository $paiementFraisRepo,
        InscriptionRepository   $inscriptionRepo,
        AnneeAcademiqueService  $anneeService,
    ): Response {
        $anneeActuelle = $anneeService->getCurrent();

        if (!$anneeActuelle) {
            $this->addFlash('warning', 'Aucune année académique courante définie.');
            return $this->redirectToRoute('app_reporting_financier');
        }

        $ayantPaye    = $paiementFraisRepo->elevesAyantPayeFrais($frais);
        $eligibles    = $inscriptionRepo->elevesEligiblesPourFrais($frais, $anneeActuelle);
        $ayantPayeIds = array_unique(array_column($ayantPaye, 'id'));
        $impayes      = array_values(array_filter($eligibles, fn($e) => !in_array($e['id'], $ayantPayeIds, true)));

        return $this->render('reporting/frais_detail.html.twig', [
            'frais'      => $frais,
            'annee'      => $anneeActuelle,
            'ayantPaye'  => $ayantPaye,
            'impayes'    => $impayes,
            'nbAttendu'  => count($eligibles),
            'totalPercu' => (float) array_sum(array_column($ayantPaye, 'totalPaye')),
        ]);
    }
}
