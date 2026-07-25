<?php

namespace App\Form;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Enum\Niveau;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClasseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la classe',
                'attr'  => ['class' => 'form-input', 'placeholder' => 'ex: 6ème A, Licence 1 Info'],
            ])
            ->add('niveau', EnumType::class, [
                'class'        => Niveau::class,
                'label'        => 'Niveau',
                'choice_label' => fn(Niveau $niveau) => $niveau->label(),
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('anneeAcademique', EntityType::class, [
                'class'        => AnneeAcademique::class,
                'choice_label' => 'libelle',
                'label'        => 'Année académique',
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('effectifMax', IntegerType::class, [
                'label'    => 'Effectif maximum',
                'required' => false,
                'attr'     => ['class' => 'form-input', 'min' => 1],
            ])
            ->add('salle', TextType::class, [
                'label'    => 'Salle',
                'required' => false,
                'attr'     => ['class' => 'form-input'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Classe::class,
        ]);
    }
}
