<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\User;
use App\Repository\TauxRepository;
use App\Form\Commande1Type;
use App\Repository\ApprovisionnementRepository;
use App\Repository\CommandeApprobateurRepository;
use App\Repository\CommandeProduitRepository;
use App\Repository\CommandeReceptionRepository;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande')]
class CommandeController extends AbstractController
{
    #[Route('/', name: 'app_commande_index', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository): Response
    {
        return $this->render('commande/index.html.twig', [
            'commandes' => $commandeRepository->findBy([], ['CommandeDate' => 'DESC']),
        ]);
    }

    #[Route('/new', name: 'app_commande_new', methods: ['GET', 'POST'])]
    public function new(Request $request,
                        EntityManagerInterface $entityManager,
                        TauxRepository $tauxRepository
    ): Response
    {
        $commande = new Commande();
        $form = $this->createForm(Commande1Type::class, $commande);
        $form->handleRequest($request);

        $tauxactif = $tauxRepository->findOneBy(['isActive' => true]);
        $request->getSession()->set('tauxactif', $tauxactif->getCout());
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commande);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/new.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/commander/{id}', name: 'app_commander', defaults: ['id' => null], methods: ['GET', 'POST'])]
    public function commander(Request $request,
                              EntityManagerInterface $entityManager,
                                CommandeRepository $commandeRepository,
                                ApprovisionnementRepository $approvisionnementRepository,
                                TauxRepository $tauxRepository,
    ): Response
    {
        $CommandeNo = $this->genererNumeroCommande(5, $commandeRepository->maxId());
        $produits = $approvisionnementRepository->stockProduit();

        $tauxactif = $tauxRepository->findOneBy(['isActive' => true]);
        $request->getSession()->set('tauxactif', $tauxactif->getCout());
        return $this->renderForm('commande/commander.html.twig', [
            'produits' => $produits,
            'numeroCommande'=> $CommandeNo
        ]);
    }

    #[Route('/{id}/approve', name: 'app_approve_commande', methods: ['GET'])]
    public function ApproveCommande(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $commande->setIsApproved(true);
        $commande->setApprovedBy($this->getUser());
        $entityManager->flush();
        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/reject', name: 'app_reject_commande', methods: ['GET'])]
    public function RejectCommande(Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $commande->setIsApproved(false);
        $commande->setApprovedBy($this->getUser());
        $entityManager->flush();
        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/submit', name: 'app_submit_commande', methods: ['GET'])]
    public function SubmitCommande(Request $request,
                                   Commande $commande,
                                   EntityManagerInterface $entityManager,
                                   CommandeRepository $commandeRepository
    ): Response
    {
        $commande->setStatus('submitted');
        $entityManager->persist($commande);
        $entityManager->flush();
        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/jsonSaveCommandeDraft', name: 'jsonSaveCommandeDraft', methods: ['GET'])]
    public function jsonSaveCommandeDraft(Request $request,
                                  CommandeRepository $commandeRepository,
                                  EntityManagerInterface $entityManager
    ): JsonResponse
    {
        try {
            $commande = new Commande();
            $commande->setCommandeDate(new \DateTime());
            $commande->setCommandePar($this->getUser());
            $commande->setStatus('draft');
            $commande->setCommandeNumber($this->genererNumeroCommande(5, $commandeRepository->maxId()));
            $entityManager->persist($commande);
            $entityManager->flush();
            return new JsonResponse([
                'etat'=>true,
                'CommandeID'=>$commande->getId()
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'etat'=>false
            ]);
        }

    }

    #[Route('/jsonSaveCommande', name: 'jsonSaveCommande', methods: ['GET'])]
    public function jsonSaveCommande(Request $request,
                                          CommandeRepository $commandeRepository,
                                          EntityManagerInterface $entityManager
    ): JsonResponse
    {
        try {
            $commande = new Commande();
            $commande->setCommandeDate(new \DateTime());
            $commande->setCommandePar($this->getUser());
            $commande->setStatus('submitted');
            $commande->setCommandeNumber($this->genererNumeroCommande(5, $commandeRepository->maxId()));
            $entityManager->persist($commande);
            $entityManager->flush();
            return new JsonResponse([
                'etat'=>true,
                'CommandeID'=>$commande->getId()
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'etat'=>false
            ]);
        }

    }


    #[Route('/jsonRecallCommande', name: 'jsonRecallCommande', methods: ['GET'])]
    public function jsonRecallCommande(Request $request,
                                     CommandeRepository $commandeRepository,
                                     EntityManagerInterface $entityManager,

    ): JsonResponse
    {
        try {
            $commandeID = $request->query->get('commandeID');
            $commande = $commandeRepository->find($commandeID);
            $commande->setStatus('cancelled');
            //$commande->setCommandeNumber($this->genererNumeroCommande(5, $countCommande));
            $entityManager->persist($commande);
            $entityManager->flush();
            return new JsonResponse([
                'etat'=>true,
                //'CommandeID'=>$commande->getId()
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'etat'=>false
            ]);
        }

    }

    function genererNumeroCommande($sequenceLength, $lastId): string {
        // Définir le préfixe
        $prefix = "COM";
        //$sequenceLength = $sequenceLength;
        $nextId = $lastId + 1;
        $formattedSequence = str_pad($nextId, $sequenceLength, "0", STR_PAD_LEFT);
        //$nomenclature = $prefix . $formattedSequence;
        return $prefix . $formattedSequence;
    }

    #[Route('/{id}/show', name: 'app_commande_show', methods: ['GET'])]
    public function show(Commande $commande,
                         CommandeReceptionRepository $commandeReceptionRepository,
                         CommandeApprobateurRepository $commandeApprobateurRepository
    ): Response
    {
        //$commandeLines = $commandeProduitRepository->findBy(['Commande' => $commande]);
        $approver = $commandeApprobateurRepository->findBy(['User'=>$this->getUser(), 'isActive'=>true]);
        $isApprover = count($approver)>0;
        //dd($commande->getId());
        $commandeLines = $commandeReceptionRepository->CommandeQuantite($commande->getId());
        return $this->render('commande/show.html.twig', [
            'commande' => $commande,
            'commandeLines'=>$commandeLines,
            'isApprover'=>$isApprover,
        ]);
    }

    #[Route('/{id}/print', name: 'app_commande_print', methods: ['GET'])]
    public function print(Commande $commande,
                         CommandeReceptionRepository $commandeReceptionRepository,
                         CommandeApprobateurRepository $commandeApprobateurRepository
    ): Response
    {
        //$commandeLines = $commandeProduitRepository->findBy(['Commande' => $commande]);
        $approver = $commandeApprobateurRepository->findBy(['User'=>$this->getUser(), 'isActive'=>true]);
        $isApprover = count($approver)>0;
        //dd($commande->getId());
        $commandeLines = $commandeReceptionRepository->CommandeQuantite($commande->getId());
        return $this->render('commande/print.html.twig', [
            'commande' => $commande,
            'commandeLines'=>$commandeLines,
            'isApprover'=>$isApprover,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Commande1Type::class, $commande);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande/edit.html.twig', [
            'commande' => $commande,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_commande_delete', methods: ['POST'])]
    public function delete(Request $request, Commande $commande, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commande->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commande);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }
}
