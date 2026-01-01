<?php

namespace App\Controller;

use App\Entity\Approvisionnement;
use App\Entity\CommandeProduit;
use App\Entity\CommandeReception;
use App\Form\CommandeReceptionType;
use App\Repository\ApprovisionnementRepository;
use App\Repository\CommandeProduitRepository;
use App\Repository\CommandeReceptionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande/reception')]
class CommandeReceptionController extends AbstractController
{
    #[Route('/', name: 'app_commande_reception_index', methods: ['GET'])]
    public function index(CommandeReceptionRepository $commandeReceptionRepository): Response
    {
        return $this->render('commande_reception/index.html.twig', [
            'commande_receptions' => $commandeReceptionRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_commande_reception_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commandeReception = new CommandeReception();
        $form = $this->createForm(CommandeReceptionType::class, $commandeReception);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commandeReception);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_reception_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande_reception/new.html.twig', [
            'commande_reception' => $commandeReception,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/receipt', name: 'app_line_receipt', methods: ['GET'])]
    public function LineReceipt(Request $request,
                                    CommandeProduit $commandeProduit,
                                    CommandeProduitRepository $commandeProduitRepository,
                                    EntityManagerInterface $entityManager,
                                    Approvisionnement $approvisionnement
    ): Response
    {
        $commandeReception = new CommandeReception();
        $commandeReception->setCommandeProduit($commandeProduit);
        $commandeReception->setQuantiteRecue($request->query->get('quantite'));
        $commandeReception->setReceptionDate(new \DateTime());
        $commandeReception->setReceivedBy($this->getUser());
        $entityManager->persist($commandeReception);
        $entityManager->flush();
        //Approvisionner
        $approvisionnement = new Approvisionnement();
        $approvisionnement->setProduit($commandeProduit->getproduit());
        $approvisionnement->setTaux($request->getSession()->get('tauxactif'));
        $approvisionnement->setQty($request->query->get('quantite'));
        $approvisionnement->setCreatedAt(new \DateTimeImmutable());
        $approvisionnement->setCreatedBy($this->getUser()->getUserIdentifier());
        $approvisionnement->setApproDate(new \DateTime());
        $approvisionnement->setCout($commandeProduit->getproduit()->getPrix() * $request->query->get('quantite'));
        $entityManager->persist($approvisionnement);

        $entityManager->flush();
        return $this->redirectToRoute('app_commande_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/liste', name: 'app_commande_reception_liste', methods: ['GET'])]
    public function liste(Request $request, CommandeReceptionRepository $commandeReceptionRepository): Response
    {
        //dd($request->query->get('commandeID'));
        $listes = $commandeReceptionRepository->HistoriqueReceptionParCommande($request->query->get('commandeID'));
        return $this->render('commande_reception/liste.html.twig', [
            'listes' => $listes,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_reception_show', methods: ['GET'])]
    public function show(CommandeReception $commandeReception): Response
    {
        return $this->render('commande_reception/show.html.twig', [
            'commande_reception' => $commandeReception,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_commande_reception_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CommandeReception $commandeReception, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeReceptionType::class, $commandeReception);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_reception_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande_reception/edit.html.twig', [
            'commande_reception' => $commandeReception,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_reception_delete', methods: ['POST'])]
    public function delete(Request $request, CommandeReception $commandeReception, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commandeReception->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commandeReception);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_reception_index', [], Response::HTTP_SEE_OTHER);
    }
}
