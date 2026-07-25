<?php

namespace App\Form;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Entity\FraisScolaire;
use App\Entity\TypeFrais;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FraisScolaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('libelle', TextType::class, [
                'label' => 'Libellé',
                'attr'  => ['class' => 'form-input', 'placeholder' => 'Ex: Frais d\'inscription'],
            ])
            ->add('montant', NumberType::class, [
                'label' => 'Montant',
                'scale' => 2,
                'attr'  => ['class' => 'form-input', 'placeholder' => '0.00'],
            ])
            ->add('type', EntityType::class, [
                'class'        => TypeFrais::class,
                'choice_label' => 'libelle',
                'query_builder' => fn ($repo) => $repo->createQueryBuilder('t')
                    ->where('t.actif = true')
                    ->orderBy('t.ordre', 'ASC'),
                'label'       => 'Type',
                'placeholder' => '— Choisir un type —',
                'required'    => false,
                'attr'        => ['class' => 'form-select'],
            ])
            ->add('mois', ChoiceType::class, [
                'label'       => 'Mois (si mensuel)',
                'required'    => false,
                'placeholder' => '— Sélectionner un mois —',
                'choices'     => [
                    'Janvier'   => 1,
                    'Février'   => 2,
                    'Mars'      => 3,
                    'Avril'     => 4,
                    'Mai'       => 5,
                    'Juin'      => 6,
                    'Juillet'   => 7,
                    'Août'      => 8,
                    'Septembre' => 9,
                    'Octobre'   => 10,
                    'Novembre'  => 11,
                    'Décembre'  => 12,
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('classes', EntityType::class, [
                'class'        => Classe::class,
                'choice_label' => fn (Classe $c) => $c->getNom() . ' (' . $c->getAnneeAcademique()->getLibelle() . ')',
                'label'        => 'Classes concernées',
                'required'     => false,
                'multiple'     => true,
                'expanded'     => false,
                'attr'         => ['class' => 'form-select'],
                'help'         => 'Laisser vide = applicable à toutes les classes.',
            ])
            ->add('anneeAcademique', EntityType::class, [
                'class'        => AnneeAcademique::class,
                'choice_label' => 'libelle',
                'label'        => 'Année académique',
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('actif', CheckboxType::class, [
                'label'    => 'Actif',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FraisScolaire::class,
        ]);
    }
}
