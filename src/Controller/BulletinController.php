<?php

namespace App\Controller;

use App\Entity\Bulletin;
use App\Repository\AnneeAcademiqueRepository;
use App\Repository\BulletinRepository;
use App\Repository\ClasseRepository;
use App\Service\AnneeAcademiqueService;
use App\Service\BulletinService;
use App\Service\PdfService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/bulletin')]
class BulletinController extends AbstractController
{
    #[Route('/', name: 'app_bulletin_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        BulletinRepository $bulletinRepository,
        ClasseRepository $classeRepository,
        AnneeAcademiqueService $anneeService,
    ): Response {
        $classeId = $request->query->get('classeId');
        $periode  = (int) $request->query->get('periode', 0);
        $annee    = $anneeService->getCurrent();
        $classes  = $classeRepository->findBy([], ['createdAt' => 'DESC']);

        $bulletins = [];

        if ($classeId && $periode && $annee) {
            $classe = $classeRepository->find($classeId);
            if ($classe) {
                $bulletins = $bulletinRepository->findByClasseAndPeriode($classe, $periode, $annee);
            }
        } elseif ($annee) {
            $bulletins = $bulletinRepository->findBy(['anneeAcademique' => $annee], ['createdAt' => 'DESC']);
        } else {
            $bulletins = $bulletinRepository->findBy([], ['createdAt' => 'DESC']);
        }

        return $this->render('bulletin/index.html.twig', [
            'bulletins' => $bulletins,
            'classes'   => $classes,
            'classeId'  => $classeId,
            'periode'   => $periode,
        ]);
    }

    #[Route('/generer-classe', name: 'app_bulletin_generer_classe', methods: ['POST'])]
    #[IsGranted('ROLE_DIRECTEUR')]
    public function genererClasse(
        Request $request,
        ClasseRepository $classeRepository,
        AnneeAcademiqueService $anneeService,
        BulletinService $bulletinService,
    ): Response {
        $classeId = $request->request->get('classeId');
        $periode  = (int) $request->request->get('periode', 0);

        $classe = $classeId ? $classeRepository->find($classeId) : null;
        if (!$classe || !$periode) {
            $this->addFlash('error', 'Veuillez sélectionner une classe et une période.');
            return $this->redirectToRoute('app_bulletin_index');
        }

        $annee = $anneeService->getCurrentOrFail();

        $bulletinService->genererPourClasse(
            $classe,
            $periode,
            $annee,
            $this->getUser()->getUserIdentifier()
        );

        $this->addFlash('success', 'Bulletins générés avec succès pour ' . $classe->getNom() . '.');

        return $this->redirectToRoute('app_bulletin_index', [
            'classeId' => $classeId,
            'periode'  => $periode,
        ]);
    }

    #[Route('/{id}', name: 'app_bulletin_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Bulletin $bulletin): Response
    {
        return $this->render('bulletin/show.html.twig', [
            'bulletin' => $bulletin,
        ]);
    }

    #[Route('/{id}/pdf', name: 'app_bulletin_pdf', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function pdf(Bulletin $bulletin, PdfService $pdfService): Response
    {
        $html = $this->renderView('bulletin/bulletin_pdf.html.twig', [
            'bulletin' => $bulletin,
        ]);

        $filename = sprintf(
            'bulletin-%s-T%d.pdf',
            strtolower(str_replace(' ', '_', $bulletin->getEleve()->getNomComplet())),
            $bulletin->getPeriode()
        );

        $pdfService->showPdfFile($html, $filename);

        return new Response();
    }

    #[Route('/{id}/valider', name: 'app_bulletin_valider', methods: ['POST'])]
    #[IsGranted('ROLE_DIRECTEUR')]
    public function valider(
        Request $request,
        Bulletin $bulletin,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->isCsrfTokenValid('valider' . $bulletin->getId(), $request->request->get('_token'))) {
            $bulletin->setStatut('validé');
            $entityManager->flush();

            $this->addFlash('success', 'Bulletin validé.');
        }

        return $this->redirectToRoute('app_bulletin_show', ['id' => $bulletin->getId()]);
    }

    #[Route('/{id}', name: 'app_bulletin_delete', methods: ['POST'])]
    #[IsGranted('ROLE_DIRECTEUR')]
    public function delete(
        Request $request,
        Bulletin $bulletin,
        EntityManagerInterface $entityManager,
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $bulletin->getId(), $request->request->get('_token'))) {
            $entityManager->remove($bulletin);
            $entityManager->flush();

            $this->addFlash('success', 'Bulletin supprimé.');
        }

        return $this->redirectToRoute('app_bulletin_index', [], Response::HTTP_SEE_OTHER);
    }
}
