<?php

namespace App\Controller;

use App\Entity\Inscription;
use App\Form\InscriptionType;
use App\Repository\AnneeAcademiqueRepository;
use App\Repository\EleveRepository;
use App\Repository\InscriptionRepository;
use App\Service\AnneeAcademiqueService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/inscription')]
class InscriptionController extends AbstractController
{
    #[Route('/', name: 'app_inscription_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        InscriptionRepository $inscriptionRepository,
        AnneeAcademiqueRepository $anneeAcademiqueRepository,
    ): Response {
        $anneeId = $request->query->get('anneeId');
        $annees  = $anneeAcademiqueRepository->findBy([], ['libelle' => 'DESC']);

        if ($anneeId) {
            $annee        = $anneeAcademiqueRepository->find($anneeId);
            $inscriptions = $annee
                ? $inscriptionRepository->findBy(['anneeAcademique' => $annee], ['createdAt' => 'DESC'])
                : $inscriptionRepository->findBy([], ['createdAt' => 'DESC']);
        } else {
            $inscriptions = $inscriptionRepository->findBy([], ['createdAt' => 'DESC']);
        }

        // Statistiques
        $parNiveau = [];
        $parStatut = [];
        foreach ($inscriptions as $i) {
            $niv = $i->getClasse()->getNiveau()->label();
            $sta = $i->getStatut() ?? 'inconnu';
            $parNiveau[$niv] = ($parNiveau[$niv] ?? 0) + 1;
            $parStatut[$sta] = ($parStatut[$sta] ?? 0) + 1;
        }

        return $this->render('inscription/index.html.twig', [
            'inscriptions' => $inscriptions,
            'annees'       => $annees,
            'anneeId'      => $anneeId,
            'nbTotal'      => count($inscriptions),
            'parNiveau'    => $parNiveau,
            'parStatut'    => $parStatut,
        ]);
    }

    #[Route('/new', name: 'app_inscription_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        InscriptionRepository $inscriptionRepository,
        EleveRepository $eleveRepository,
        AnneeAcademiqueService $anneeService,
    ): Response {
        $annee = $anneeService->getCurrent();
        if (!$annee) {
            $this->addFlash('error', 'Aucune année académique active. Veuillez en activer une avant de créer une inscription.');
            return $this->redirectToRoute('app_annee_academique_index');
        }

        $inscription = new Inscription();
        $inscription->setAnneeAcademique($annee);

        $eleveId = $request->query->get('eleveId');
        if ($eleveId) {
            $eleve = $eleveRepository->find($eleveId);
            if ($eleve) {
                $inscription->setEleve($eleve);
            }
        }

        $form = $this->createForm(InscriptionType::class, $inscription, [
            'annee_courante' => $annee,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Forcer l'année active (le form ne la contient plus)
            $inscription->setAnneeAcademique($annee);

            $existing = $inscriptionRepository->findOneBy([
                'eleve'           => $inscription->getEleve(),
                'anneeAcademique' => $annee,
            ]);

            if ($existing) {
                $this->addFlash('error', 'Cet élève est déjà inscrit pour l\'année ' . $annee->getLibelle() . '.');
                return $this->render('inscription/new.html.twig', [
                    'inscription' => $inscription,
                    'form'        => $form->createView(),
                    'annee'       => $annee,
                ]);
            }

            $entityManager->persist($inscription);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription enregistrée pour l\'année ' . $annee->getLibelle() . '.');

            return $this->redirectToRoute('app_inscription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inscription/new.html.twig', [
            'inscription' => $inscription,
            'form'        => $form->createView(),
            'annee'       => $annee,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_inscription_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function edit(
        Request $request,
        Inscription $inscription,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(InscriptionType::class, $inscription, [
            'annee_courante' => $inscription->getAnneeAcademique(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Inscription modifiée avec succès.');

            return $this->redirectToRoute('app_inscription_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('inscription/edit.html.twig', [
            'inscription' => $inscription,
            'form'        => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_inscription_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function delete(
        Request $request,
        Inscription $inscription,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $inscription->getId(), $request->request->get('_token'))) {
            $entityManager->remove($inscription);
            $entityManager->flush();

            $this->addFlash('success', 'Inscription supprimée.');
        }

        return $this->redirectToRoute('app_inscription_index', [], Response::HTTP_SEE_OTHER);
    }
}
