<?php

namespace App\Controller;

use App\Entity\Produits;
use App\Form\ProduitsType;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/produits')]
class ProduitsController extends AbstractController
{
    #[Route('/', name: 'app_produits_index', methods: ['GET'])]
    public function index(ProduitsRepository $produitsRepository): Response
    {
        return $this->render('produits/index.html.twig', [
            'produits' => $produitsRepository->findAll(),
        ]);
    }

    #[Route('/', name: 'app_produits_excel', methods: ['GET'])]
    public function excel(ProduitsRepository $produitsRepository): void
    {
        $produits = $produitsRepository->findAll();

    }

    #[Route('/new', name: 'app_produits_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager
    , ProduitsRepository $produitsRepository): Response
    {
        $produit = new Produits();
        $code = $this->genererCodeProduit(6, count($produitsRepository->findAll()));
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($produit);
            $entityManager->flush();
            $this->addFlash('success', "Produit créé avec succès");

            return $this->redirectToRoute('app_produits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produits/new.html.twig', [
            'produit' => $produit,
            'form' => $form,
            'code'=> $code
        ]);
    }

    #[Route('/{id}', name: 'app_produits_show', methods: ['GET'])]
    public function show(Produits $produit): Response
    {
        return $this->render('produits/show.html.twig', [
            'produit' => $produit,
        ]);
    }


    #[Route('/jsonGetProduct2', name: 'jsonGetProduct2', methods: ['GET'])]
    public function jsonGetProduct2(Request $request): Response
    {
        return new JsonResponse([
            'etat' => true,
        ]);
    }



    #[Route('/{id}/edit', name: 'app_produits_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Produits $produit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProduitsType::class, $produit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', "Produit modifié avec succès");
            return $this->redirectToRoute('app_produits_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('produits/edit.html.twig', [
            'produit' => $produit,
            'form' => $form,
        ]);
    }

    function genererCodeProduit($sequenceLength, $lastId) {
        // Définir le préfixe
        $prefix = "CD";
        $sequenceLength = $sequenceLength;
        $nextId = $lastId + 1;
        $formattedSequence = str_pad($nextId, $sequenceLength, "0", STR_PAD_LEFT);
        $nomenclature = $prefix . $formattedSequence;
        return $nomenclature;
    }

    #[Route('/{id}', name: 'app_produits_delete', methods: ['POST'])]
    public function delete(Request $request, Produits $produit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$produit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($produit);
            $entityManager->flush();
            $this->addFlash('success', "Produit supprimé avec succès");

        }

        return $this->redirectToRoute('app_produits_index', [], Response::HTTP_SEE_OTHER);
    }
}
