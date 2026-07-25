<?php

namespace App\Form;

use App\Entity\Eleve;
use App\Entity\EmpruntLivre;
use App\Entity\Livre;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmpruntLivreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('livre', EntityType::class, [
                'class'        => Livre::class,
                'choice_label' => fn (Livre $l) => $l->getTitre() . ' — ' . $l->getAuteur(),
                'label'        => 'Livre',
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('emprunteur', EntityType::class, [
                'class'        => Eleve::class,
                'choice_label' => fn (Eleve $e) => $e->getNomComplet(),
                'label'        => 'Élève emprunteur',
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('dateEmprunt', DateType::class, [
                'label'  => "Date d'emprunt",
                'widget' => 'single_text',
                'data'   => new \DateTime(),
            ])
            ->add('dateRetourPrevue', DateType::class, [
                'label'  => 'Date de retour prévue',
                'widget' => 'single_text',
            ])
            ->add('observations', TextareaType::class, [
                'label'    => 'Observations',
                'required' => false,
                'attr'     => ['class' => 'form-input', 'rows' => 2],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EmpruntLivre::class,
        ]);
    }
}
