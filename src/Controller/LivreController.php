<?php

namespace App\Controller;

use App\Entity\Livre;
use App\Form\LivreType;
use App\Repository\EmpruntLivreRepository;
use App\Repository\LivreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/bibliotheque/livres')]
class LivreController extends AbstractController
{
    #[Route('/', name: 'app_livre_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, LivreRepository $livreRepository): Response
    {
        $q = $request->query->get('q', '');
        $livres = $q
            ? $livreRepository->search($q)
            : $livreRepository->findActifs();

        return $this->render('livre/index.html.twig', [
            'livres' => $livres,
            'q'      => $q,
        ]);
    }

    #[Route('/new', name: 'app_livre_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $livre = new Livre();
        $form  = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($livre);
            $entityManager->flush();

            $this->addFlash('success', 'Livre ajouté avec succès.');

            return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livre/new.html.twig', [
            'livre' => $livre,
            'form'  => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_livre_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Livre $livre, EmpruntLivreRepository $empruntRepository, LivreRepository $livreRepository): Response
    {
        $empruntsEnCours = $empruntRepository->createQueryBuilder('e')
            ->where('e.livre = :livre')
            ->andWhere('e.statut IN (:statuts)')
            ->setParameter('livre', $livre)
            ->setParameter('statuts', ['en_cours', 'en_retard'])
            ->orderBy('e.dateRetourPrevue', 'ASC')
            ->getQuery()
            ->getResult();

        return $this->render('livre/show.html.twig', [
            'livre'          => $livre,
            'empruntsEnCours' => $empruntsEnCours,
            'disponibles'    => $livreRepository->countDisponibles($livre),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_livre_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function edit(Request $request, Livre $livre, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(LivreType::class, $livre);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Livre modifié avec succès.');

            return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('livre/edit.html.twig', [
            'livre' => $livre,
            'form'  => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_livre_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function delete(Request $request, Livre $livre, EntityManagerInterface $entityManager, EmpruntLivreRepository $empruntRepository): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $livre->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_livre_index');
        }

        $empruntsActifs = $empruntRepository->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.livre = :livre')
            ->andWhere('e.statut IN (:statuts)')
            ->setParameter('livre', $livre)
            ->setParameter('statuts', ['en_cours', 'en_retard'])
            ->getQuery()
            ->getSingleScalarResult();

        if ($empruntsActifs > 0) {
            $this->addFlash('error', 'Impossible de supprimer ce livre : il y a des emprunts en cours.');

            return $this->redirectToRoute('app_livre_show', ['id' => $livre->getId()]);
        }

        $entityManager->remove($livre);
        $entityManager->flush();

        $this->addFlash('success', 'Livre supprimé.');

        return $this->redirectToRoute('app_livre_index', [], Response::HTTP_SEE_OTHER);
    }
}
