<?php

namespace App\Service;

use App\Entity\AnneeAcademique;
use App\Repository\AnneeAcademiqueRepository;

class AnneeAcademiqueService
{
    public function __construct(private readonly AnneeAcademiqueRepository $repo) {}

    public function getCurrent(): ?AnneeAcademique
    {
        return $this->repo->findCurrent();
    }

    public function getCurrentOrFail(): AnneeAcademique
    {
        $annee = $this->repo->findCurrent();
        if ($annee === null) {
            throw new \LogicException("Aucune année académique active n'est définie. Veuillez en activer une dans Paramètres > Année académique.");
        }
        return $annee;
    }
}
