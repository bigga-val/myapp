<?php

namespace App\Controller;

use App\Repository\AuditLogRepository;
use App\Repository\CommandeRepository;
use App\Repository\CreditRepository;
use App\Repository\DebitRepository;
use App\Repository\EmployeRepository;
use App\Repository\PaieEmployeRepository;
use App\Repository\ProduitVenduRepository;
use App\Repository\VenteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/board', name: 'app_dashboard')]
    public function index(
        VenteRepository       $venteRepo,
        CommandeRepository    $commandeRepo,
        CreditRepository      $creditRepo,
        DebitRepository       $debitRepo,
        PaieEmployeRepository $paieEmployeRepo,
        ProduitVenduRepository $produitVenduRepo,
        EmployeRepository     $employeRepo,
        AuditLogRepository    $auditRepo,
    ): Response {
        // KPIs
        $caHier           = $venteRepo->caHier();
        $nbVentesHier     = $venteRepo->countHier();
        $caMois           = $venteRepo->caMoisCourant();
        $nbVentes         = $venteRepo->countMoisCourant();
        $commandesAttente = $commandeRepo->countEnAttente();
        $masseSalariale   = $paieEmployeRepo->masseSalarialeMoisCourant();
        $totalCredits     = $creditRepo->totalMoisCourant();
        $totalDebits      = $debitRepo->totalMoisCourant();
        $nbEmployes       = count($employeRepo->findAll());

        // Graphique CA 6 derniers mois
        $caParMoisRaw = $venteRepo->caParMois(6);
        $caLabels     = array_column($caParMoisRaw, 'mois');
        $caData       = array_map(fn($r) => round((float)$r['montant'], 2), $caParMoisRaw);

        // Graphique ventes par jour du mois courant
        $joursRaw    = $venteRepo->ventesParJourDuMois();
        $joursLabels = array_map(fn($r) => (int) ltrim((string)$r['jour'], '0') ?: 0, $joursRaw);
        $joursData   = array_map(fn($r) => round((float)$r['montant'], 2), $joursRaw);

        // Top 5 produits
        $topProduits = $produitVenduRepo->topProduits(5);

        // Tableaux récents
        $dernieresVentes    = $venteRepo->dernieres(5);
        $dernieresCommandes = $commandeRepo->dernieres(5);
        $derniersLogs       = $auditRepo->findFiltered(null, null, null, null, null);
        $derniersLogs       = array_slice($derniersLogs, 0, 8);

        return $this->render('index.html.twig', [
            'caHier'           => $caHier,
            'nbVentesHier'     => $nbVentesHier,
            'caMois'           => $caMois,
            'nbVentes'         => $nbVentes,
            'commandesAttente' => $commandesAttente,
            'masseSalariale'   => $masseSalariale,
            'totalCredits'     => $totalCredits,
            'totalDebits'      => $totalDebits,
            'nbEmployes'       => $nbEmployes,
            'caLabels'         => json_encode($caLabels),
            'caData'           => json_encode($caData),
            'joursLabels'      => json_encode($joursLabels),
            'joursData'        => json_encode($joursData),
            'topProduits'      => $topProduits,
            'dernieresVentes'  => $dernieresVentes,
            'dernieresCommandes' => $dernieresCommandes,
            'derniersLogs'     => $derniersLogs,
            'moisCourant'      => (new \DateTime())->format('F Y'),
        ]);
    }
}
