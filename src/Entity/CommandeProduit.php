<?php

namespace App\Entity;

use App\Repository\CommandeProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeProduitRepository::class)]
class CommandeProduit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?Commande $Commande = null;

    #[ORM\ManyToOne]
    private ?Produits $Produit = null;

    #[ORM\Column(nullable: true)]
    private ?float $Quantite = null;

    #[ORM\Column(nullable: true)]
    private ?float $PrixUnitaire = null;

    #[ORM\Column(nullable: true)]
    private ?float $taux = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommande(): ?Commande
    {
        return $this->Commande;
    }

    public function setCommande(?Commande $Commande): static
    {
        $this->Commande = $Commande;

        return $this;
    }

    public function getProduit(): ?Produits
    {
        return $this->Produit;
    }

    public function setProduit(?Produits $Produit): static
    {
        $this->Produit = $Produit;

        return $this;
    }

    public function getQuantite(): ?float
    {
        return $this->Quantite;
    }

    public function setQuantite(?float $Quantite): static
    {
        $this->Quantite = $Quantite;

        return $this;
    }

    public function getPrixUnitaire(): ?float
    {
        return $this->PrixUnitaire;
    }

    public function setPrixUnitaire(?float $PrixUnitaire): static
    {
        $this->PrixUnitaire = $PrixUnitaire;

        return $this;
    }

    public function getTaux(): ?float
    {
        return $this->taux;
    }

    public function setTaux(?float $taux): static
    {
        $this->taux = $taux;

        return $this;
    }
}
