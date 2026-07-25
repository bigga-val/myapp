<?php

namespace App\Controller;

use App\Entity\EmpruntLivre;
use App\Form\EmpruntLivreType;
use App\Repository\EmpruntLivreRepository;
use App\Repository\LivreRepository;
use App\Service\EmpruntRetardService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/bibliotheque/emprunts')]
class EmpruntLivreController extends AbstractController
{
    #[Route('/', name: 'app_emprunt_livre_index', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function index(
        Request $request,
        EmpruntLivreRepository $empruntRepository,
        EntityManagerInterface $entityManager,
        EmpruntRetardService $retardService,
    ): Response {
        // Mettre à jour les statuts de retard à chaque visite
        $retardService->mettreAJourRetards($entityManager, $empruntRepository);

        $statut = $request->query->get('statut', '');

        if ($statut === 'en_cours') {
            $emprunts = $empruntRepository->createQueryBuilder('e')
                ->where('e.statut = :s')
                ->setParameter('s', 'en_cours')
                ->orderBy('e.dateRetourPrevue', 'ASC')
                ->getQuery()
                ->getResult();
        } elseif ($statut === 'en_retard') {
            $emprunts = $empruntRepository->findEnRetard();
        } elseif ($statut === 'rendu') {
            $emprunts = $empruntRepository->createQueryBuilder('e')
                ->where('e.statut = :s')
                ->setParameter('s', 'rendu')
                ->orderBy('e.dateRetourEffective', 'DESC')
                ->getQuery()
                ->getResult();
        } else {
            $emprunts = $empruntRepository->findBy([], ['createdAt' => 'DESC']);
        }

        return $this->render('emprunt_livre/index.html.twig', [
            'emprunts' => $emprunts,
            'statut'   => $statut,
        ]);
    }

    #[Route('/new', name: 'app_emprunt_livre_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        LivreRepository $livreRepository,
    ): Response {
        $emprunt = new EmpruntLivre();
        $form    = $this->createForm(EmpruntLivreType::class, $emprunt);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $livre = $emprunt->getLivre();

            if ($livre && $livreRepository->countDisponibles($livre) <= 0) {
                $this->addFlash('error', 'Aucun exemplaire disponible pour ce livre.');

                return $this->redirectToRoute('app_emprunt_livre_new');
            }

            $emprunt->setStatut('en_cours');
            $emprunt->setEnregistrePar($this->getUser()->getUserIdentifier());

            $entityManager->persist($emprunt);
            $entityManager->flush();

            $this->addFlash('success', 'Emprunt enregistré avec succès.');

            return $this->redirectToRoute('app_emprunt_livre_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('emprunt_livre/new.html.twig', [
            'emprunt' => $emprunt,
            'form'    => $form->createView(),
        ]);
    }

    #[Route('/{id}/retour', name: 'app_emprunt_livre_retour', methods: ['POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function retour(Request $request, EmpruntLivre $emprunt, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('retour' . $emprunt->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_emprunt_livre_index');
        }

        $emprunt->setDateRetourEffective(new \DateTime('today'));
        $emprunt->setStatut('rendu');
        $entityManager->flush();

        $this->addFlash('success', 'Retour enregistré.');

        return $this->redirectToRoute('app_emprunt_livre_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'app_emprunt_livre_delete', methods: ['POST'])]
    #[IsGranted('ROLE_SECRETAIRE')]
    public function delete(Request $request, EmpruntLivre $emprunt, EntityManagerInterface $entityManager): Response
    {
        if (!$this->isCsrfTokenValid('delete' . $emprunt->getId(), $request->request->get('_token'))) {
            $this->addFlash('error', 'Token CSRF invalide.');

            return $this->redirectToRoute('app_emprunt_livre_index');
        }

        $entityManager->remove($emprunt);
        $entityManager->flush();

        $this->addFlash('success', 'Emprunt supprimé.');

        return $this->redirectToRoute('app_emprunt_livre_index', [], Response::HTTP_SEE_OTHER);
    }
}
