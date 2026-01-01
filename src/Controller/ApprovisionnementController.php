<?php

namespace App\Controller;

use App\Entity\Approvisionnement;
use App\Form\ApprovisionnementType;
use App\Repository\ApprovisionnementRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/approvisionnement')]
class ApprovisionnementController extends AbstractController
{
    #[Route('/', name: 'app_approvisionnement_index', methods: ['GET'])]
    public function index(ApprovisionnementRepository $approvisionnementRepository): Response
    {
        return $this->render('approvisionnement/index.html.twig', [
            'approvisionnements' => $approvisionnementRepository->findAll(),
        ]);
    }

    #[Route('/stock', name: 'app_approvisionnement_stock', methods: ['GET'])]
    public function stock(ApprovisionnementRepository $approvisionnementRepository): Response
    {
        $appros = $approvisionnementRepository->stockProduit();
        //dd($appros);
        return $this->render('approvisionnement/stock.html.twig', [
            'appros'=>$appros
        ]);
    }

    #[Route('/new', name: 'app_approvisionnement_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $approvisionnement = new Approvisionnement();
        $form = $this->createForm(ApprovisionnementType::class, $approvisionnement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $approvisionnement->setApproDate(new \DateTime());
            $approvisionnement->setCreatedAt(new \DateTimeImmutable());
            $approvisionnement->setCreatedBy($this->getUser()->getUsername());
            $approvisionnement->setTaux($request->getSession()->get('tauxactif'));

            $entityManager->persist($approvisionnement);
            $entityManager->flush();
            $this->addFlash('success', "Produit approvisionné avec succès");

            return $this->redirectToRoute('app_approvisionnement_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('approvisionnement/new.html.twig', [
            'approvisionnement' => $approvisionnement,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_approvisionnement_show', methods: ['GET'])]
    public function show(Approvisionnement $approvisionnement): Response
    {
        return $this->render('approvisionnement/show.html.twig', [
            'approvisionnement' => $approvisionnement,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_approvisionnement_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Approvisionnement $approvisionnement, EntityManagerInterface $entityManager): Response
    {
        if (in_array('ROLE_ADMIN', $this->getUser()->getRoles(), true)) {
            // User is admin, display admin-specific content
            $form = $this->createForm(ApprovisionnementType::class, $approvisionnement);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', "Modifications prises en charge avec succès");

                return $this->redirectToRoute('app_approvisionnement_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('approvisionnement/edit.html.twig', [
                'approvisionnement' => $approvisionnement,
                'form' => $form,
            ]);
        } else {
            return $this->redirectToRoute('erreur401', [], Response::HTTP_SEE_OTHER);

        }

    }

    #[Route('/{id}', name: 'app_approvisionnement_delete', methods: ['POST'])]
    public function delete(Request $request, Approvisionnement $approvisionnement, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$approvisionnement->getId(), $request->request->get('_token'))) {
            $this->addFlash('success',  "Suppression effectuée avec succès");

            $entityManager->remove($approvisionnement);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_approvisionnement_index', [], Response::HTTP_SEE_OTHER);
    }
}
