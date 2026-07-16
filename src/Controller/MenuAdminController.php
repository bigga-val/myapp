<?php

namespace App\Controller;

use App\Repository\CategorieProduitRepository;
use App\Repository\ProduitsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/menu-restaurant')]
#[IsGranted('ROLE_ADMIN')]
class MenuAdminController extends AbstractController
{
    #[Route('/', name: 'app_menu_admin_index', methods: ['GET'])]
    public function index(
        CategorieProduitRepository $categorieProduitRepository,
        ProduitsRepository $produitsRepository,
    ): Response {
        $categories = $categorieProduitRepository->findAll();
        $produits   = $produitsRepository->findBy([], ['designation' => 'ASC']);

        $menu = [];
        foreach ($categories as $categorie) {
            $produitsCategorie = array_values(array_filter(
                $produits,
                fn($p) => $p->getCategorie()?->getId() === $categorie->getId()
            ));
            $menu[] = [
                'categorie' => $categorie,
                'produits'  => $produitsCategorie,
            ];
        }

        // Produits sans catégorie
        $sansCat = array_values(array_filter($produits, fn($p) => $p->getCategorie() === null));

        return $this->render('menu_admin/index.html.twig', [
            'menu'    => $menu,
            'sansCat' => $sansCat,
            'total'   => count(array_filter($produits, fn($p) => $p->isMenu())),
        ]);
    }

    #[Route('/toggle/{id}', name: 'app_menu_admin_toggle', methods: ['POST'])]
    public function toggle(
        int $id,
        Request $request,
        ProduitsRepository $produitsRepository,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        if (!$this->isCsrfTokenValid('menu_toggle_' . $id, $request->request->get('_token'))) {
            return new JsonResponse(['etat' => false, 'message' => 'Token invalide'], 403);
        }

        $produit = $produitsRepository->find($id);
        if (!$produit) {
            return new JsonResponse(['etat' => false, 'message' => 'Produit introuvable'], 404);
        }

        $produit->setIsMenu(!$produit->isMenu());
        $entityManager->flush();

        return new JsonResponse([
            'etat'   => true,
            'isMenu' => $produit->isMenu(),
        ]);
    }
}
