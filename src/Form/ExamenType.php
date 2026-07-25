<?php

namespace App\Form;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Entity\Examen;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExamenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'label' => 'Libellé',
                'attr'  => ['class' => 'form-input'],
            ])
            ->add('type', ChoiceType::class, [
                'label'   => 'Type',
                'choices' => [
                    'Devoir'         => 'devoir',
                    'Interrogation'  => 'interro',
                    'Examen Partiel' => 'examen_partiel',
                    'Examen Final'   => 'examen_final',
                ],
                'attr' => ['class' => 'form-select'],
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
            ->add('dateDebut', DateType::class, [
                'label'    => 'Date de début',
                'widget'   => 'single_text',
                'required' => false,
            ])
            ->add('dateFin', DateType::class, [
                'label'    => 'Date de fin',
                'widget'   => 'single_text',
                'required' => false,
            ])
            ->add('anneeAcademique', EntityType::class, [
                'class'        => AnneeAcademique::class,
                'choice_label' => 'libelle',
                'label'        => 'Année académique',
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('classe', EntityType::class, [
                'class'        => Classe::class,
                'choice_label' => 'nom',
                'label'        => 'Classe',
                'required'     => false,
                'placeholder'  => 'Toutes les classes',
                'attr'         => ['class' => 'form-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Examen::class,
        ]);
    }
}
