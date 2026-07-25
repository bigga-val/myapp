<?php

namespace App\Controller;

use App\Repository\AuditLogRepository;
use App\Repository\ClasseRepository;
use App\Repository\CreditRepository;
use App\Repository\DebitRepository;
use App\Repository\EleveRepository;
use App\Repository\EmpruntLivreRepository;
use App\Repository\EmployeRepository;
use App\Repository\FraisScolaireRepository;
use App\Repository\InscriptionRepository;
use App\Repository\PaieEmployeRepository;
use App\Repository\PaiementFraisRepository;
use App\Service\AnneeAcademiqueService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    #[Route('/board', name: 'app_dashboard')]
    #[IsGranted('ROLE_USER')]
    public function index(
        CreditRepository        $creditRepo,
        DebitRepository         $debitRepo,
        PaieEmployeRepository   $paieEmployeRepo,
        EmployeRepository       $employeRepo,
        AuditLogRepository      $auditRepo,
        EleveRepository         $eleveRepo,
        ClasseRepository        $classeRepo,
        InscriptionRepository   $inscriptionRepo,
        AnneeAcademiqueService  $anneeService,
        PaiementFraisRepository $paiementRepo,
        EmpruntLivreRepository  $empruntRepo,
    ): Response {
        $masseSalariale = $paieEmployeRepo->masseSalarialeMoisCourant();
        $totalCredits   = $creditRepo->totalMoisCourant();
        $totalDebits    = $debitRepo->totalMoisCourant();
        $nbEnseignants  = $employeRepo->countAll();
        $derniersLogs   = array_slice($auditRepo->findFiltered(null, null, null, null, null), 0, 8);

        $nbEleves  = $eleveRepo->countAll();
        $annee     = $anneeService->getCurrent();
        $nbClasses = $annee ? count($classeRepo->findBy(['anneeAcademique' => $annee])) : 0;

        $moisCourant    = (int) (new \DateTime())->format('m');
        $anneeCourante  = (int) (new \DateTime())->format('Y');
        $fraisPercusMois = $paiementRepo->sumPercusMois($anneeCourante, $moisCourant);
        $fraisEnAttente  = $annee ? $paiementRepo->countEnAttente($annee) : 0;

        return $this->render('index.html.twig', [
            'masseSalariale'   => $masseSalariale,
            'totalCredits'     => $totalCredits,
            'totalDebits'      => $totalDebits,
            'nbEnseignants'    => $nbEnseignants,
            'derniersLogs'     => $derniersLogs,
            'moisCourant'      => (new \DateTime())->format('F Y'),
            'nbEleves'         => $nbEleves,
            'nbClasses'        => $nbClasses,
            'fraisPercusMois'  => $fraisPercusMois,
            'fraisEnAttente'   => $fraisEnAttente,
            'livresEmpruntes'  => $empruntRepo->countEnCours(),
        ]);
    }
}
