<?php

namespace App\Form;


use App\Entity\CategorieProduit;
use App\Entity\Produits;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;


class ProduitsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('code')
            ->add('designation')
            ->add('Categorie', EntityType::class, [
                'class' => CategorieProduit::class,
                'choice_label' => 'designation',
            ])
            ->add('prix')
            ->add('maximum')
            ->add('minimum')
            ->add('uniteMesure')
            //->add('fabricant')
            ->add('preemption', DateTimeType::class, [
                'date_label' => 'Starts On',
                'widget'=>'single_text'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produits::class,
        ]);
    }
}
