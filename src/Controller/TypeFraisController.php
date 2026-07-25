<?php

namespace App\Controller;

use App\Entity\TypeFrais;
use App\Form\TypeFraisType;
use App\Repository\TypeFraisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/type-frais')]
#[IsGranted('ROLE_ADMIN')]
class TypeFraisController extends AbstractController
{
    #[Route('', name: 'app_type_frais_index', methods: ['GET'])]
    public function index(TypeFraisRepository $repo): Response
    {
        return $this->render('type_frais/index.html.twig', [
            'types' => $repo->findBy([], ['ordre' => 'ASC', 'libelle' => 'ASC']),
        ]);
    }

    #[Route('/new', name: 'app_type_frais_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $typeFrais = new TypeFrais();
        $form = $this->createForm(TypeFraisType::class, $typeFrais);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($typeFrais);
            $em->flush();
            $this->addFlash('success', 'Type de frais créé.');
            return $this->redirectToRoute('app_type_frais_index');
        }

        return $this->render('type_frais/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_type_frais_edit', methods: ['GET', 'POST'])]
    public function edit(TypeFrais $typeFrais, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(TypeFraisType::class, $typeFrais);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Type de frais modifié.');
            return $this->redirectToRoute('app_type_frais_index');
        }

        return $this->render('type_frais/edit.html.twig', [
            'typeFrais' => $typeFrais,
            'form'      => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_type_frais_delete', methods: ['POST'])]
    public function delete(TypeFrais $typeFrais, Request $request, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $typeFrais->getId(), $request->request->get('_token'))) {
            $em->remove($typeFrais);
            $em->flush();
            $this->addFlash('success', 'Type de frais supprimé.');
        }

        return $this->redirectToRoute('app_type_frais_index');
    }
}
