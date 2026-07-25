<?php

namespace App\Form;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Entity\Employe;
use App\Entity\EmploiDuTemps;
use App\Entity\Matiere;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmploiDuTempsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('classe', EntityType::class, [
                'class'        => Classe::class,
                'choice_label' => fn(Classe $c) => $c->getNom() . ' (' . $c->getNiveau()->label() . ')',
                'label'        => 'Classe',
            ])
            ->add('matiere', EntityType::class, [
                'class'        => Matiere::class,
                'choice_label' => fn(Matiere $m) => $m->getNom() . ' (coef ' . $m->getCoefficient() . ')',
                'label'        => 'Matière',
            ])
            ->add('enseignant', EntityType::class, [
                'class'        => Employe::class,
                'choice_label' => 'nomcomplet',
                'label'        => 'Enseignant',
            ])
            ->add('jourSemaine', ChoiceType::class, [
                'choices' => [
                    'Lundi'   => 1,
                    'Mardi'   => 2,
                    'Mercredi' => 3,
                    'Jeudi'   => 4,
                    'Vendredi' => 5,
                    'Samedi'  => 6,
                ],
                'label' => 'Jour',
            ])
            ->add('heureDebut', TimeType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime',
                'label'  => 'Heure de début',
            ])
            ->add('heureFin', TimeType::class, [
                'widget' => 'single_text',
                'input'  => 'datetime',
                'label'  => 'Heure de fin',
            ])
            ->add('salle', TextType::class, [
                'required' => false,
                'label'    => 'Salle',
            ])
            ->add('anneeAcademique', EntityType::class, [
                'class'        => AnneeAcademique::class,
                'choice_label' => 'libelle',
                'label'        => 'Année académique',
            ])
            ->add('actif', CheckboxType::class, [
                'required' => false,
                'label'    => 'Actif',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmploiDuTemps::class,
        ]);
    }
}
