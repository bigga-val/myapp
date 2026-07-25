<?php

namespace App\Form;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Examen;
use App\Entity\Matiere;
use App\Entity\Note;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NoteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('eleve', EntityType::class, [
                'class'        => Eleve::class,
                'choice_label' => fn (Eleve $e) => $e->getNomComplet(),
                'label'        => 'Élève',
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('matiere', EntityType::class, [
                'class'        => Matiere::class,
                'choice_label' => fn (Matiere $m) => $m->getNom() . ' (coef ' . $m->getCoefficient() . ')',
                'label'        => 'Matière',
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('classe', EntityType::class, [
                'class'        => Classe::class,
                'choice_label' => 'nom',
                'label'        => 'Classe',
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('examen', EntityType::class, [
                'class'        => Examen::class,
                'choice_label' => 'libelle',
                'label'        => 'Examen',
                'required'     => false,
                'placeholder'  => 'Aucun',
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('valeur', NumberType::class, [
                'label' => 'Note',
                'scale' => 2,
                'attr'  => ['class' => 'form-input', 'min' => 0, 'max' => 20, 'step' => '0.5'],
            ])
            ->add('valeurMax', NumberType::class, [
                'label' => 'Note maximale',
                'scale' => 2,
                'attr'  => ['class' => 'form-input', 'value' => 20],
            ])
            ->add('periode', ChoiceType::class, [
                'label'   => 'Période (trimestre)',
                'choices' => [
                    '1er Trimestre'  => 1,
                    '2ème Trimestre' => 2,
                    '3ème Trimestre' => 3,
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('anneeAcademique', EntityType::class, [
                'class'        => AnneeAcademique::class,
                'choice_label' => 'libelle',
                'label'        => 'Année académique',
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('observationsProf', TextareaType::class, [
                'label'    => 'Observations du professeur',
                'required' => false,
                'attr'     => ['class' => 'form-input', 'rows' => 2],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
        ]);
    }
}
