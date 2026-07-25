<?php

namespace App\Controller;

use App\Entity\Note;
use App\Form\NoteType;
use App\Repository\ClasseRepository;
use App\Repository\InscriptionRepository;
use App\Repository\MatiereRepository;
use App\Repository\NoteRepository;
use App\Service\AnneeAcademiqueService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/note')]
class NoteController extends AbstractController
{
    #[Route('/', name: 'app_note_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        NoteRepository $noteRepository,
        ClasseRepository $classeRepository,
        AnneeAcademiqueService $anneeService,
    ): Response {
        $classeId = $request->query->get('classeId');
        $periode  = (int) $request->query->get('periode', 0);
        $annee    = $anneeService->getCurrent();
        $classes  = $classeRepository->findBy([], ['createdAt' => 'DESC']);

        $notes = [];
        $classe = null;

        if ($classeId && $periode && $annee) {
            $classe = $classeRepository->find($classeId);
            if ($classe) {
                $notes = $noteRepository->findByClasseAndPeriode($classe, $periode, $annee);
            }
        } elseif ($annee) {
            $notes = $noteRepository->findBy(['anneeAcademique' => $annee], ['createdAt' => 'DESC']);
        } else {
            $notes = $noteRepository->findBy([], ['createdAt' => 'DESC']);
        }

        return $this->render('note/index.html.twig', [
            'notes'    => $notes,
            'classes'  => $classes,
            'classeId' => $classeId,
            'periode'  => $periode,
        ]);
    }

    #[Route('/new', name: 'app_note_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        AnneeAcademiqueService $anneeService,
    ): Response {
        $note = new Note();

        $annee = $anneeService->getCurrent();
        if ($annee) {
            $note->setAnneeAcademique($annee);
        }

        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $note->setSaisiePar($this->getUser()->getUserIdentifier());
            $entityManager->persist($note);
            $entityManager->flush();

            $this->addFlash('success', 'Note enregistrée avec succès.');

            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('note/new.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_note_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function edit(
        Request $request,
        Note $note,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Note modifiée avec succès.');

            return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('note/edit.html.twig', [
            'note' => $note,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_note_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function delete(
        Request $request,
        Note $note,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $note->getId(), $request->request->get('_token'))) {
            $entityManager->remove($note);
            $entityManager->flush();

            $this->addFlash('success', 'Note supprimée.');
        }

        return $this->redirectToRoute('app_note_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/saisie/{classeId}/{periode}', name: 'app_note_saisie_classe', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function saisieParClasse(
        int $classeId,
        int $periode,
        Request $request,
        ClasseRepository $classeRepository,
        InscriptionRepository $inscriptionRepository,
        MatiereRepository $matiereRepository,
        NoteRepository $noteRepository,
        EntityManagerInterface $entityManager,
        AnneeAcademiqueService $anneeService,
    ): Response {
        $classe = $classeRepository->find($classeId);
        if (!$classe) {
            throw $this->createNotFoundException('Classe introuvable.');
        }

        $annee = $anneeService->getCurrentOrFail();

        $inscriptions = $inscriptionRepository->findByClasse($classe);
        $matieres     = $matiereRepository->findByNiveau($classe->getNiveau());

        // Load existing notes indexed by [eleveId][matiereId]
        $existingNotes = $noteRepository->findByClasseAndPeriode($classe, $periode, $annee);
        $noteMap = [];
        foreach ($existingNotes as $n) {
            $noteMap[$n->getEleve()->getId()][$n->getMatiere()->getId()] = $n;
        }

        if ($request->isMethod('POST')) {
            $data = $request->request->all('notes');
            $saisieParIdent = $this->getUser()->getUserIdentifier();

            foreach ($inscriptions as $inscription) {
                $eleve = $inscription->getEleve();
                $eleveId = $eleve->getId();

                foreach ($matieres as $matiere) {
                    $matiereId = $matiere->getId();
                    $rawVal = $data[$eleveId][$matiereId] ?? null;

                    if ($rawVal === null || $rawVal === '') {
                        continue;
                    }

                    $valeur = (float) $rawVal;
                    if ($valeur < 0) { $valeur = 0; }
                    if ($valeur > 20) { $valeur = 20; }

                    if (isset($noteMap[$eleveId][$matiereId])) {
                        $note = $noteMap[$eleveId][$matiereId];
                        $note->setValeur($valeur);
                    } else {
                        $note = new Note();
                        $note->setEleve($eleve);
                        $note->setMatiere($matiere);
                        $note->setClasse($classe);
                        $note->setPeriode($periode);
                        $note->setAnneeAcademique($annee);
                        $note->setValeur($valeur);
                        $note->setValeurMax(20.0);
                        $note->setSaisiePar($saisieParIdent);
                        $entityManager->persist($note);
                    }
                }
            }

            $entityManager->flush();
            $this->addFlash('success', 'Notes enregistrées avec succès.');

            return $this->redirectToRoute('app_note_saisie_classe', [
                'classeId' => $classeId,
                'periode'  => $periode,
            ]);
        }

        return $this->render('note/saisie_classe.html.twig', [
            'classe'       => $classe,
            'periode'      => $periode,
            'inscriptions' => $inscriptions,
            'matieres'     => $matieres,
            'noteMap'      => $noteMap,
            'annee'        => $annee,
        ]);
    }
}
