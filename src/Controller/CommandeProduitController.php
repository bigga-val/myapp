<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\CommandeProduit;
use App\Form\CommandeProduitType;
use App\Repository\CommandeProduitRepository;
use App\Repository\CommandeRepository;
use App\Repository\ProduitsRepository;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande/produit')]
class CommandeProduitController extends AbstractController
{
    #[Route('/', name: 'app_commande_produit_index', methods: ['GET'])]
    public function index(CommandeProduitRepository $commandeProduitRepository): Response
    {
        return $this->render('commande_produit/index.html.twig', [
            'commande_produits' => $commandeProduitRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commande_produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commandeProduit = new CommandeProduit();
        $form = $this->createForm(CommandeProduitType::class, $commandeProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commandeProduit);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande_produit/new.html.twig', [
            'commande_produit' => $commandeProduit,
            'form' => $form,
        ]);
    }

    #[Route('/jsonSaveLigneCommande', name: 'jsonSaveLigneCommande', methods: ['GET'])]
    public function jsonSaveLigneCommande(Request $request,
                                       CommandeRepository $commandeRepository,
                                       ProduitsRepository $produitsRepository,
                                       EntityManagerInterface $entityManager
    ): JsonResponse
    {
        try {
            $produit = $produitsRepository->find($request->query->get('produitID'));
            $Commandeproduit = new Commandeproduit();
            $Commandeproduit->setQuantite($request->query->get('qty'));
            $Commandeproduit->setCommande($commandeRepository->find($request->query->get('commandeID')));
            $Commandeproduit->setProduit($produit);
            $Commandeproduit->setPrixUnitaire($produit->getPrix());
            $Commandeproduit->setTaux($request->getSession()->get('tauxactif'));
            $entityManager->persist($Commandeproduit);
            $entityManager->flush();
            return new JsonResponse([
                'etat'=>true,
                'CommandeproduitID'=>$Commandeproduit->getId()
            ]);
        }catch (Exception $e){
            return new JsonResponse([
                'etat'=>false
            ]);
        }

    }

    #[Route('/{id}', name: 'app_commande_produit_show', methods: ['GET'])]
    public function show(CommandeProduit $commandeProduit): Response
    {
        return $this->render('commande_produit/show.html.twig', [
            'commande_produit' => $commandeProduit,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_produit_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CommandeProduit $commandeProduit, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeProduitType::class, $commandeProduit);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_produit_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande_produit/edit.html.twig', [
            'commande_produit' => $commandeProduit,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_produit_delete', methods: ['POST'])]
    public function delete(Request $request, CommandeProduit $commandeProduit, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commandeProduit->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commandeProduit);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_produit_index', [], Response::HTTP_SEE_OTHER);
    }
}
