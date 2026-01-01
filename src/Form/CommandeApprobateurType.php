<?php

namespace App\Form;

use App\Entity\CommandeApprobateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CommandeApprobateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('User', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CommandeApprobateur::class,
        ]);
    }
}
