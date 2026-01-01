<?php

namespace App\Controller;

use App\Entity\CommandeApprobateur;
use App\Form\CommandeApprobateurType;
use App\Repository\CommandeApprobateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/commande/approbateur')]
class CommandeApprobateurController extends AbstractController
{
    #[Route('/', name: 'app_commande_approbateur_index', methods: ['GET', 'POST'])]
    public function index(CommandeApprobateurRepository $commandeApprobateurRepository,Request $request, EntityManagerInterface $entityManager): Response
    {
        $commandeApprobateur = new CommandeApprobateur();
        $form = $this->createForm(CommandeApprobateurType::class, $commandeApprobateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commandeApprobateur->setIsActive(true);
            $entityManager->persist($commandeApprobateur);
            $entityManager->flush();

            //return $this->redirectToRoute('app_commande_approbateur_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('commande_approbateur/index.html.twig', [
            'commande_approbateurs' => $commandeApprobateurRepository->findAll(),
            'form' => $form,
            'commande_approbateur' => $commandeApprobateur,
        ]);
    }

    #[Route('/new', name: 'app_commande_approbateur_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $commandeApprobateur = new CommandeApprobateur();
        $form = $this->createForm(CommandeApprobateurType::class, $commandeApprobateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($commandeApprobateur);
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_approbateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande_approbateur/new.html.twig', [
            'commande_approbateur' => $commandeApprobateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_approbateur_show', methods: ['GET'])]
    public function show(CommandeApprobateur $commandeApprobateur): Response
    {
        return $this->render('commande_approbateur/show.html.twig', [
            'commande_approbateur' => $commandeApprobateur,
        ]);
    }

    #[Route('/{id}/activer', name: 'app_commande_approbateur_activer', methods: ['GET'])]
    public function activer(CommandeApprobateur $commandeApprobateur, EntityManagerInterface $entityManager): Response
    {
        If($commandeApprobateur->isIsActive()){
            $commandeApprobateur->setIsActive(false);
        }else{
            $commandeApprobateur->setIsActive(true);
        }
        $entityManager->persist($commandeApprobateur);
        $entityManager->flush();
        return $this->redirectToRoute('app_commande_approbateur_index');
    }

    #[Route('/{id}/edit', name: 'app_commande_approbateur_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CommandeApprobateur $commandeApprobateur, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CommandeApprobateurType::class, $commandeApprobateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_commande_approbateur_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('commande_approbateur/edit.html.twig', [
            'commande_approbateur' => $commandeApprobateur,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_commande_approbateur_delete', methods: ['POST'])]
    public function delete(Request $request, CommandeApprobateur $commandeApprobateur, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$commandeApprobateur->getId(), $request->request->get('_token'))) {
            $entityManager->remove($commandeApprobateur);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_commande_approbateur_index', [], Response::HTTP_SEE_OTHER);
    }
}
