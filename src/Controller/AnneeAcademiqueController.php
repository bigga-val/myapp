<?php

namespace App\Controller;

use App\Entity\AnneeAcademique;
use App\Form\AnneeAcademiqueType;
use App\Repository\AnneeAcademiqueRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/annee-academique')]
class AnneeAcademiqueController extends AbstractController
{
    #[Route('/', name: 'app_annee_academique_index', methods: ['GET'])]
    public function index(AnneeAcademiqueRepository $anneeAcademiqueRepository): Response
    {
        return $this->render('annee_academique/index.html.twig', [
            'annees' => $anneeAcademiqueRepository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_annee_academique_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $annee = new AnneeAcademique();
        $form = $this->createForm(AnneeAcademiqueType::class, $annee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Garantir qu'une nouvelle année ne démarre pas comme "en cours"
            $annee->setIsCurrent(false);
            $entityManager->persist($annee);
            $entityManager->flush();

            $this->addFlash('success', 'Année académique créée. Utilisez "Activer" pour en faire l\'année en cours.');

            return $this->redirectToRoute('app_annee_academique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annee_academique/new.html.twig', [
            'annee' => $annee,
            'form'  => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_annee_academique_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, AnneeAcademique $annee, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(AnneeAcademiqueType::class, $annee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Année académique modifiée avec succès.');

            return $this->redirectToRoute('app_annee_academique_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('annee_academique/edit.html.twig', [
            'annee' => $annee,
            'form'  => $form->createView(),
        ]);
    }

    #[Route('/{id}/activer', name: 'app_annee_academique_activer', methods: ['POST'])]
    public function activer(Request $request, AnneeAcademique $annee, AnneeAcademiqueRepository $anneeAcademiqueRepository, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('activer'.$annee->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_annee_academique_index');
        }

        // Désactiver toutes les années
        foreach ($anneeAcademiqueRepository->findAll() as $a) {
            $a->setIsCurrent(false);
        }

        // Activer l'année sélectionnée
        $annee->setIsCurrent(true);
        $entityManager->flush();

        $this->addFlash('success', "L'année {$annee->getLibelle()} est maintenant l'année en cours.");

        return $this->redirectToRoute('app_annee_academique_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_annee_academique_delete', methods: ['POST'])]
    public function delete(Request $request, AnneeAcademique $annee, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$annee->getId(), $request->request->get('_token'))) {
            $entityManager->remove($annee);
            $entityManager->flush();

            $this->addFlash('success', 'Année académique supprimée.');
        }

        return $this->redirectToRoute('app_annee_academique_index', [], Response::HTTP_SEE_OTHER);
    }
}
