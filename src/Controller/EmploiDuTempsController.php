<?php

namespace App\Controller;

use App\Entity\EmploiDuTemps;
use App\Form\EmploiDuTempsType;
use App\Repository\ClasseRepository;
use App\Repository\EmploiDuTempsRepository;
use App\Service\AnneeAcademiqueService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/emploi-du-temps')]
class EmploiDuTempsController extends AbstractController
{
    #[Route('/', name: 'app_emploi_du_temps_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        EmploiDuTempsRepository $edtRepository,
        ClasseRepository $classeRepository,
    ): Response {
        $classeId = $request->query->get('classeId');
        $classes  = $classeRepository->findBy([], ['createdAt' => 'DESC']);
        $classe   = null;
        $slots    = [];

        if ($classeId) {
            $classe = $classeRepository->find($classeId);
            if ($classe) {
                $slots = $edtRepository->findBy(['classe' => $classe], ['jourSemaine' => 'ASC', 'heureDebut' => 'ASC']);
            }
        } else {
            $slots = $edtRepository->findBy([], ['createdAt' => 'DESC']);
        }

        return $this->render('emploi_du_temps/index.html.twig', [
            'slots'    => $slots,
            'classes'  => $classes,
            'classe'   => $classe,
            'classeId' => $classeId,
        ]);
    }

    #[Route('/{classeId}/semaine', name: 'app_emploi_du_temps_semaine', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function semaine(
        int $classeId,
        ClasseRepository $classeRepository,
        EmploiDuTempsRepository $edtRepository,
        AnneeAcademiqueService $anneeService,
    ): Response {
        $classe = $classeRepository->find($classeId);
        if (!$classe) {
            throw $this->createNotFoundException('Classe introuvable.');
        }

        $annee = $anneeService->getCurrentOrFail();
        $grouped = $edtRepository->findByClasseGroupedByDay($classe, $annee);

        return $this->render('emploi_du_temps/semaine.html.twig', [
            'classe'  => $classe,
            'grouped' => $grouped,
            'annee'   => $annee,
        ]);
    }

    #[Route('/new', name: 'app_emploi_du_temps_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function new(
        Request $request,
        EmploiDuTempsRepository $edtRepository,
        EntityManagerInterface $entityManager,
        AnneeAcademiqueService $anneeService,
        ClasseRepository $classeRepository,
    ): Response {
        $edt = new EmploiDuTemps();

        $annee = $anneeService->getCurrent();
        if ($annee) {
            $edt->setAnneeAcademique($annee);
        }

        // Pre-fill classe from query param
        $classeIdParam = $request->query->get('classeId');
        if ($classeIdParam) {
            $classeParam = $classeRepository->find($classeIdParam);
            if ($classeParam) {
                $edt->setClasse($classeParam);
            }
        }

        $form = $this->createForm(EmploiDuTempsType::class, $edt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($edtRepository->hasConflitEnseignant(
                $edt->getEnseignant(),
                $edt->getJourSemaine(),
                $edt->getHeureDebut(),
                $edt->getHeureFin(),
                $edt->getAnneeAcademique(),
                $edt->getId()
            )) {
                $this->addFlash('error', 'Conflit : cet enseignant est déjà programmé à ce créneau.');
            } else {
                $entityManager->persist($edt);
                $entityManager->flush();
                $this->addFlash('success', 'Créneau ajouté avec succès.');

                return $this->redirectToRoute('app_emploi_du_temps_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('emploi_du_temps/new.html.twig', [
            'edt'  => $edt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_emploi_du_temps_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function edit(
        Request $request,
        EmploiDuTemps $edt,
        EmploiDuTempsRepository $edtRepository,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(EmploiDuTempsType::class, $edt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($edtRepository->hasConflitEnseignant(
                $edt->getEnseignant(),
                $edt->getJourSemaine(),
                $edt->getHeureDebut(),
                $edt->getHeureFin(),
                $edt->getAnneeAcademique(),
                $edt->getId()
            )) {
                $this->addFlash('error', 'Conflit : cet enseignant est déjà programmé à ce créneau.');
            } else {
                $entityManager->flush();
                $this->addFlash('success', 'Créneau modifié avec succès.');

                return $this->redirectToRoute('app_emploi_du_temps_index', [], Response::HTTP_SEE_OTHER);
            }
        }

        return $this->renderForm('emploi_du_temps/edit.html.twig', [
            'edt'  => $edt,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_emploi_du_temps_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function delete(
        Request $request,
        EmploiDuTemps $edt,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $edt->getId(), $request->request->get('_token'))) {
            $entityManager->remove($edt);
            $entityManager->flush();
            $this->addFlash('success', 'Créneau supprimé.');
        }

        return $this->redirectToRoute('app_emploi_du_temps_index', [], Response::HTTP_SEE_OTHER);
    }
}
