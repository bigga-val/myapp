<?php

namespace App\Controller;

use App\Entity\Eleve;
use App\Form\EleveType;
use App\Repository\EleveRepository;
use App\Repository\FraisScolaireRepository;
use App\Repository\InscriptionRepository;
use App\Repository\PaiementFraisRepository;
use App\Service\AnneeAcademiqueService;
use App\Service\MatriculeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/eleve')]
class EleveController extends AbstractController
{
    #[Route('/', name: 'app_eleve_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(EleveRepository $eleveRepository): Response
    {
        $eleves    = $eleveRepository->findBy([], ['nom' => 'ASC']);
        $nbGarcons = count(array_filter($eleves, fn($e) => $e->getSexe() === 'M'));
        $nbFilles  = count($eleves) - $nbGarcons;
        $avecPhoto = count(array_filter($eleves, fn($e) => $e->getPhoto() !== null));

        return $this->render('eleve/index.html.twig', [
            'eleves'    => $eleves,
            'nbTotal'   => count($eleves),
            'nbGarcons' => $nbGarcons,
            'nbFilles'  => $nbFilles,
            'avecPhoto' => $avecPhoto,
        ]);
    }

    #[Route('/new', name: 'app_eleve_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        MatriculeService $matriculeService,
    ): Response {
        $eleve = new Eleve();
        $form  = $this->createForm(EleveType::class, $eleve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/eleves',
                    $newFilename
                );
                $eleve->setPhoto('uploads/eleves/' . $newFilename);
            }

            $entityManager->persist($eleve);
            $entityManager->flush();

            $eleve->setMatricule($matriculeService->generateEleveMatricule($eleve->getId()));
            $entityManager->flush();

            $this->addFlash('success', 'Élève créé avec succès. Matricule : ' . $eleve->getMatricule());

            return $this->redirectToRoute('app_eleve_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('eleve/new.html.twig', [
            'eleve' => $eleve,
            'form'  => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_eleve_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(
        Eleve $eleve,
        InscriptionRepository $inscriptionRepository,
        PaiementFraisRepository $paiementRepository,
        FraisScolaireRepository $fraisRepository,
        AnneeAcademiqueService $anneeService,
    ): Response {
        $annee = $anneeService->getCurrent();
        $fraisEligibles = [];

        if ($annee) {
            $inscription = $inscriptionRepository->findOneBy(['eleve' => $eleve, 'anneeAcademique' => $annee]);
            $classe = $inscription?->getClasse();
            $fraisApplicables = $fraisRepository->findByClasseAndAnnee($classe, $annee);
            $dejaPayesIds = array_map(
                fn($p) => $p->getFraisScolaire()->getId(),
                $paiementRepository->findBy(['eleve' => $eleve])
            );
            $fraisEligibles = array_values(array_filter(
                $fraisApplicables,
                fn($f) => !in_array($f->getId(), $dejaPayesIds)
            ));
        }

        return $this->render('eleve/show.html.twig', [
            'eleve'          => $eleve,
            'inscriptions'   => $inscriptionRepository->findByEleve($eleve),
            'paiements'      => $paiementRepository->findByEleve($eleve),
            'fraisEligibles' => $fraisEligibles,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_eleve_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function edit(Request $request, Eleve $eleve, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EleveType::class, $eleve);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photoFile')->getData();
            if ($photoFile) {
                $newFilename = uniqid() . '.' . $photoFile->guessExtension();
                $photoFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/eleves',
                    $newFilename
                );
                $eleve->setPhoto('uploads/eleves/' . $newFilename);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Élève modifié avec succès.');

            return $this->redirectToRoute('app_eleve_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('eleve/edit.html.twig', [
            'eleve' => $eleve,
            'form'  => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_eleve_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function delete(Request $request, Eleve $eleve, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $eleve->getId(), $request->request->get('_token'))) {
            $entityManager->remove($eleve);
            $entityManager->flush();

            $this->addFlash('success', 'Élève supprimé.');
        }

        return $this->redirectToRoute('app_eleve_index', [], Response::HTTP_SEE_OTHER);
    }
}
