<?php

namespace App\Controller;

use App\Entity\FraisScolaire;
use App\Form\FraisScolaireType;
use App\Repository\AnneeAcademiqueRepository;
use App\Repository\FraisScolaireRepository;
use App\Repository\PaiementFraisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/frais-scolaires', name: 'app_frais_scolaire_')]
class FraisScolaireController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        FraisScolaireRepository $fraisRepo,
        AnneeAcademiqueRepository $anneeRepo,
    ): Response {
        $anneeId = $request->query->get('anneeId');
        $annee   = $anneeId ? $anneeRepo->find($anneeId) : null;

        if ($annee) {
            $frais = $fraisRepo->findByAnnee($annee);
        } else {
            $frais = $fraisRepo->findBy([], ['createdAt' => 'DESC']);
        }

        $annees = $anneeRepo->findBy([], ['createdAt' => 'DESC']);

        return $this->render('frais_scolaire/index.html.twig', [
            'frais'       => $frais,
            'annees'      => $annees,
            'anneeActive' => $annee,
        ]);
    }

    #[Route('/new', name: 'new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $frais = new FraisScolaire();
        $form  = $this->createForm(FraisScolaireType::class, $frais);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($frais);
            $em->flush();

            $this->addFlash('success', 'Frais scolaire créé avec succès.');

            return $this->redirectToRoute('app_frais_scolaire_index');
        }

        return $this->render('frais_scolaire/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function edit(Request $request, FraisScolaire $frais, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(FraisScolaireType::class, $frais);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            $this->addFlash('success', 'Frais scolaire modifié avec succès.');

            return $this->redirectToRoute('app_frais_scolaire_index');
        }

        return $this->render('frais_scolaire/edit.html.twig', [
            'frais' => $frais,
            'form'  => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'delete', methods: ['POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function delete(
        Request $request,
        FraisScolaire $frais,
        EntityManagerInterface $em,
        PaiementFraisRepository $paiementRepo,
    ): Response {
        if (!$this->isCsrfTokenValid('delete' . $frais->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_frais_scolaire_index');
        }

        $paiementsLies = $paiementRepo->findBy(['fraisScolaire' => $frais]);
        if (count($paiementsLies) > 0) {
            $this->addFlash('error', 'Impossible de supprimer ce frais : des paiements y sont liés.');

            return $this->redirectToRoute('app_frais_scolaire_index');
        }

        $em->remove($frais);
        $em->flush();

        $this->addFlash('success', 'Frais scolaire supprimé.');

        return $this->redirectToRoute('app_frais_scolaire_index');
    }
}
