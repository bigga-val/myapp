<?php

namespace App\Form;

use App\Entity\Eleve;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EleveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr'  => ['class' => 'form-input'],
            ])
            ->add('postnom', TextType::class, [
                'label'    => 'Post-nom',
                'required' => false,
                'attr'     => ['class' => 'form-input'],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr'  => ['class' => 'form-input'],
            ])
            ->add('sexe', ChoiceType::class, [
                'label'   => 'Sexe',
                'choices' => ['Masculin' => 'M', 'Féminin' => 'F'],
                'attr'    => ['class' => 'form-select'],
            ])
            ->add('dateNaissance', DateType::class, [
                'label'    => 'Date de naissance',
                'widget'   => 'single_text',
                'required' => false,
            ])
            ->add('lieuNaissance', TextType::class, [
                'label'    => 'Lieu de naissance',
                'required' => false,
                'attr'     => ['class' => 'form-input'],
            ])
            ->add('adresse', TextareaType::class, [
                'label'    => 'Adresse',
                'required' => false,
                'attr'     => ['class' => 'form-input', 'rows' => 2],
            ])
            ->add('nomTuteur', TextType::class, [
                'label'    => 'Nom du tuteur',
                'required' => false,
                'attr'     => ['class' => 'form-input'],
            ])
            ->add('telephoneTuteur', TextType::class, [
                'label'    => 'Téléphone tuteur',
                'required' => false,
                'attr'     => ['class' => 'form-input'],
            ])
            ->add('emailTuteur', EmailType::class, [
                'label'    => 'Email tuteur',
                'required' => false,
                'attr'     => ['class' => 'form-input'],
            ])
            ->add('relationTuteur', ChoiceType::class, [
                'label'    => 'Relation',
                'required' => false,
                'choices'  => [
                    'Père'          => 'père',
                    'Mère'          => 'mère',
                    'Oncle'         => 'oncle',
                    'Tante'         => 'tante',
                    'Grand-parent'  => 'grand-parent',
                    'Tuteur légal'  => 'tuteur légal',
                    'Autre'         => 'autre',
                ],
                'placeholder' => 'Choisir...',
                'attr'        => ['class' => 'form-select'],
            ])
            ->add('photoFile', FileType::class, [
                'label'       => 'Photo',
                'mapped'      => false,
                'required'    => false,
                'constraints' => [
                    new File([
                        'maxSize'   => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Veuillez uploader une image valide (JPEG, PNG ou WebP).',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Eleve::class,
        ]);
    }
}
