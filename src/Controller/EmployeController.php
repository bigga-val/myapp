<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Entity\Paie;
use App\Entity\PaieEmploye;
use App\Form\EmployeType;
use App\Repository\EmployeRepository;
use App\Repository\PaieEmployeRepository;
use App\Repository\PaieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/employe')]
class EmployeController extends AbstractController
{
    #[Route('/', name: 'app_employe_index', methods: ['GET'])]
    public function index(EmployeRepository $employeRepository): Response
    {
        return $this->render('employe/index.html.twig', [
            'employes' => $employeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_employe_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $employe = new Employe();
        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($employe);
            $entityManager->flush();

            return $this->redirectToRoute('app_employe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('employe/new.html.twig', [
            'employe' => $employe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_employe_show', methods: ['GET'])]
    public function show(Employe $employe): Response
    {
        return $this->render('employe/show.html.twig', [
            'employe' => $employe,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_employe_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Employe $employe, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(EmployeType::class, $employe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_employe_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('employe/edit.html.twig', [
            'employe' => $employe,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/payer', name: 'app_employe_payer', methods: ['GET', 'POST'])]
    public function payer(Request $request,
                          Employe $employe, PaieRepository $paieRepository,
                          PaieEmployeRepository $paieEmployeRepository,
                          EntityManagerInterface $entityManager,

    ): Response
    {
        $fraisapayer = $paieRepository->findAll();
        $fraispayes = $paieEmployeRepository->findBy(['Employe'=>$employe]);
        if ($request->isMethod('POST')) {
            $frais = $paieRepository->find($request->request->get('frais'));
            $fraispaye = new PaieEmploye();
            $fraispaye->setEmploye($employe);
            $fraispaye->setPaie($frais);
            $fraispaye->setTotal($request->request->get('montant'));
            $fraispaye->setCreatedAt(new \DateTimeImmutable('now'));
            $entityManager->persist($fraispaye);
            $entityManager->flush();
            return $this->redirectToRoute("app_employe_index");
        }
        return $this->renderForm('employe/payer.html.twig', [
            'employe' => $employe,
            'frais'=>$fraisapayer,
            'fraispayes'=>$fraispayes
        ]);
    }

    #[Route('/{id}', name: 'app_employe_delete', methods: ['POST'])]
    public function delete(Request $request, Employe $employe, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$employe->getId(), $request->request->get('_token'))) {
            $entityManager->remove($employe);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_employe_index', [], Response::HTTP_SEE_OTHER);
    }
}
