<?php

namespace App\Controller;

use App\Entity\ProduitVendu;
use App\Entity\Vente;
use App\Repository\CategorieProduitRepository;
use App\Repository\ProduitsRepository;
use App\Repository\TableRepository;
use App\Repository\TauxRepository;
use App\Repository\VenteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/menu')]
class MenuPublicController extends AbstractController
{
    /**
     * Page publique du menu — accessible sans connexion.
     * Un seul QR code pour tout le restaurant ; la table est choisie au moment de la commande.
     */
    #[Route('', name: 'app_menu_public', methods: ['GET'])]
    public function index(
        TableRepository $tableRepository,
        CategorieProduitRepository $categorieProduitRepository,
        ProduitsRepository $produitsRepository,
    ): Response {
        // Uniquement les produits marqués comme "menu"
        $produits = $produitsRepository->findBy(['isMenu' => true], ['designation' => 'ASC']);

        $categories = $categorieProduitRepository->findAll();

        $menu = [];
        foreach ($categories as $categorie) {
            $produitsCategorie = array_values(array_filter(
                $produits,
                fn($p) => $p->getCategorie()?->getId() === $categorie->getId()
            ));
            if (!empty($produitsCategorie)) {
                $menu[] = [
                    'categorie' => $categorie,
                    'produits'  => $produitsCategorie,
                ];
            }
        }

        $tables = $tableRepository->findBy([], ['designation' => 'ASC']);

        return $this->render('menu_public/index.html.twig', [
            'menu'   => $menu,
            'tables' => $tables,
        ]);
    }

    /**
     * Réception de la commande client (POST JSON).
     * Corps attendu : { items: [{produitId, qty}], tableId, clientNom, clientTel }
     */
    #[Route('/commander', name: 'app_menu_commander', methods: ['POST'])]
    public function commander(
        Request $request,
        TableRepository $tableRepository,
        VenteRepository $venteRepository,
        ProduitsRepository $produitsRepository,
        TauxRepository $tauxRepository,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
        $data  = json_decode($request->getContent(), true);
        $items = $data['items'] ?? [];

        if (empty($items)) {
            return new JsonResponse(['etat' => false, 'message' => 'Commande vide'], 400);
        }

        $tableId   = $data['tableId'] ?? null;
        $clientNom = trim($data['clientNom'] ?? '');
        $clientTel = trim($data['clientTel'] ?? '');

        if (!$tableId) {
            return new JsonResponse(['etat' => false, 'message' => 'Veuillez choisir une table'], 400);
        }

        $table = $tableRepository->find((int) $tableId);
        if (!$table) {
            return new JsonResponse(['etat' => false, 'message' => 'Table introuvable'], 404);
        }

        try {
            $tauxActif = $tauxRepository->findOneBy(['isActive' => true]);
            $taux      = $tauxActif ? $tauxActif->getCout() : 1;

            $label = $clientNom ?: 'Client';

            $vente = new Vente();
            $vente->setTableServie($table);
            $vente->setCreatedBy($label . ' - ' . $table->getDesignation());
            $vente->setClientNom($clientNom ?: 'Client');
            $vente->setClientTel($clientTel ?: null);
            $vente->setVenteDate(new \DateTime());
            $vente->setCreatedAt(new \DateTimeImmutable());
            $vente->setStatusVente('progress');
            $vente->setNumeroVente($this->genererNumeroVente(5, count($venteRepository->findAll())));

            $entityManager->persist($vente);

            foreach ($items as $item) {
                $produit = $produitsRepository->find((int) $item['produitId']);
                if (!$produit) {
                    continue;
                }

                $ligne = new ProduitVendu();
                $ligne->setVente($vente);
                $ligne->setProduit($produit);
                $ligne->setQty((float) $item['qty']);
                $ligne->setPrixUnitaire($produit->getPrix());
                $ligne->setTaux($taux);
                $ligne->setCreatedAt(new \DateTimeImmutable());
                $ligne->setCreatedby($label . ' - ' . $table->getDesignation());

                $entityManager->persist($ligne);
            }

            $entityManager->flush();

            return new JsonResponse([
                'etat'        => true,
                'venteId'     => $vente->getId(),
                'numeroVente' => $vente->getNumeroVente(),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['etat' => false, 'message' => 'Erreur lors de la commande'], 500);
        }
    }

    private function genererNumeroVente(int $sequenceLength, int $lastId): string
    {
        return 'V' . str_pad($lastId + 1, $sequenceLength, '0', STR_PAD_LEFT);
    }
}
