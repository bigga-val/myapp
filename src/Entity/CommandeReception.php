<?php

namespace App\Entity;

use App\Repository\CommandeReceptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeReceptionRepository::class)]
class CommandeReception
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    private ?CommandeProduit $CommandeProduit = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $ReceptionDate = null;

    #[ORM\ManyToOne]
    private ?User $ReceivedBy = null;

    #[ORM\Column(nullable: true)]
    private ?float $QuantiteRecue = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommandeProduit(): ?CommandeProduit
    {
        return $this->CommandeProduit;
    }

    public function setCommandeProduit(?CommandeProduit $CommandeProduit): static
    {
        $this->CommandeProduit = $CommandeProduit;

        return $this;
    }

    public function getReceptionDate(): ?\DateTimeInterface
    {
        return $this->ReceptionDate;
    }

    public function setReceptionDate(?\DateTimeInterface $ReceptionDate): static
    {
        $this->ReceptionDate = $ReceptionDate;

        return $this;
    }

    public function getReceivedBy(): ?User
    {
        return $this->ReceivedBy;
    }

    public function setReceivedBy(?User $ReceivedBy): static
    {
        $this->ReceivedBy = $ReceivedBy;

        return $this;
    }

    public function getQuantiteRecue(): ?float
    {
        return $this->QuantiteRecue;
    }

    public function setQuantiteRecue(?float $QuantiteRecue): static
    {
        $this->QuantiteRecue = $QuantiteRecue;

        return $this;
    }
}
