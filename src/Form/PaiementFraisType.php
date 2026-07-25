<?php

namespace App\Form;

use App\Entity\Eleve;
use App\Entity\FraisScolaire;
use App\Entity\PaiementFrais;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PaiementFraisType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('eleve', EntityType::class, [
                'class'        => Eleve::class,
                'choice_label' => function (Eleve $e) use ($options) {
                    $classe = $options['classes_par_eleve'][$e->getId()] ?? null;
                    $label  = $e->getNomComplet() . ' (' . ($e->getMatricule() ?? '—') . ')';
                    return $classe ? $label . ' — ' . $classe : $label;
                },
                'label'        => 'Élève',
                'placeholder'  => '— Choisir un élève —',
                'required'     => true,
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('fraisScolaire', EntityType::class, [
                'class'        => FraisScolaire::class,
                'choice_label' => 'libelle',
                'label'        => 'Frais scolaire',
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('montantPaye', NumberType::class, [
                'label' => 'Montant payé',
                'scale' => 2,
                'attr'  => ['class' => 'form-input', 'placeholder' => '0.00'],
            ])
            ->add('datePaiement', DateType::class, [
                'label'  => 'Date de paiement',
                'widget' => 'single_text',
                'data'   => new \DateTime(),
            ])
            ->add('modePaiement', ChoiceType::class, [
                'label'   => 'Mode de paiement',
                'choices' => [
                    'Espèces'      => 'especes',
                    'Mobile Money' => 'mobile_money',
                    'Virement'     => 'virement',
                    'Chèque'       => 'cheque',
                ],
                'attr' => ['class' => 'form-select'],
            ])
            ->add('observations', TextareaType::class, [
                'label'    => 'Observations',
                'required' => false,
                'attr'     => ['class' => 'form-input', 'rows' => 3],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'       => PaiementFrais::class,
            'classes_par_eleve' => [],  // [eleveId => 'Nom de la classe']
        ]);
    }
}
