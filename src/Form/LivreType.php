<?php

namespace App\Form;

use App\Entity\Livre;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr'  => ['class' => 'form-input'],
            ])
            ->add('auteur', TextType::class, [
                'label' => 'Auteur',
                'attr'  => ['class' => 'form-input'],
            ])
            ->add('isbn', TextType::class, [
                'label'    => 'ISBN',
                'required' => false,
                'attr'     => ['class' => 'form-input'],
            ])
            ->add('categorie', TextType::class, [
                'label'    => 'Catégorie',
                'required' => false,
                'attr'     => ['class' => 'form-input', 'placeholder' => 'Ex: Roman, Sciences, Histoire'],
            ])
            ->add('anneePublication', IntegerType::class, [
                'label'    => 'Année de publication',
                'required' => false,
                'attr'     => ['class' => 'form-input'],
            ])
            ->add('nombreExemplaires', IntegerType::class, [
                'label' => "Nombre d'exemplaires",
                'attr'  => ['class' => 'form-input', 'min' => 1],
            ])
            ->add('localisation', TextType::class, [
                'label'    => 'Localisation',
                'required' => false,
                'attr'     => ['class' => 'form-input', 'placeholder' => 'Ex: Rayon A, Étagère 3'],
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description',
                'required' => false,
                'attr'     => ['class' => 'form-input', 'rows' => 3],
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
            'data_class' => Livre::class,
        ]);
    }
}
