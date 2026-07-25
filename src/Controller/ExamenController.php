<?php

namespace App\Controller;

use App\Entity\Examen;
use App\Form\ExamenType;
use App\Repository\ExamenRepository;
use App\Service\AnneeAcademiqueService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/examen')]
class ExamenController extends AbstractController
{
    #[Route('/', name: 'app_examen_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        ExamenRepository $examenRepository,
        AnneeAcademiqueService $anneeService,
    ): Response {
        $annee   = $anneeService->getCurrent();
        $examens = $annee
            ? $examenRepository->findByAnneeAndClasse($annee)
            : $examenRepository->findBy([], ['periode' => 'ASC', 'createdAt' => 'DESC']);

        // Group by periode
        $grouped = [];
        foreach ($examens as $examen) {
            $grouped[$examen->getPeriode()][] = $examen;
        }
        ksort($grouped);

        return $this->render('examen/index.html.twig', [
            'grouped' => $grouped,
            'annee'   => $annee,
        ]);
    }

    #[Route('/new', name: 'app_examen_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        AnneeAcademiqueService $anneeService,
    ): Response {
        $examen = new Examen();

        $annee = $anneeService->getCurrent();
        if ($annee) {
            $examen->setAnneeAcademique($annee);
        }

        $form = $this->createForm(ExamenType::class, $examen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($examen);
            $entityManager->flush();

            $this->addFlash('success', 'Examen créé avec succès.');

            return $this->redirectToRoute('app_examen_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('examen/new.html.twig', [
            'examen' => $examen,
            'form'   => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_examen_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function edit(
        Request $request,
        Examen $examen,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(ExamenType::class, $examen);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Examen modifié avec succès.');

            return $this->redirectToRoute('app_examen_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('examen/edit.html.twig', [
            'examen' => $examen,
            'form'   => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_examen_delete', methods: ['POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function delete(
        Request $request,
        Examen $examen,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $examen->getId(), $request->request->get('_token'))) {
            $entityManager->remove($examen);
            $entityManager->flush();

            $this->addFlash('success', 'Examen supprimé.');
        }

        return $this->redirectToRoute('app_examen_index', [], Response::HTTP_SEE_OTHER);
    }
}
