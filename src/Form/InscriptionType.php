<?php

namespace App\Form;

use App\Entity\AnneeAcademique;
use App\Entity\Classe;
use App\Entity\Eleve;
use App\Entity\Inscription;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var AnneeAcademique|null $annee */
        $annee = $options['annee_courante'];

        $builder
            ->add('eleve', EntityType::class, [
                'class'        => Eleve::class,
                'choice_label' => fn (Eleve $e) => $e->getNomComplet() . ' (' . ($e->getMatricule() ?? '—') . ')',
                'label'        => 'Élève',
                'placeholder'  => '— Choisir un élève —',
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('classe', EntityType::class, [
                'class'         => Classe::class,
                'choice_label'  => fn (Classe $c) => $c->getNom() . ' — ' . $c->getNiveau()->label(),
                'label'         => 'Classe',
                'placeholder'   => '— Choisir une classe —',
                'attr'          => ['class' => 'form-select'],
                'query_builder' => function (EntityRepository $er) use ($annee) {
                    $qb = $er->createQueryBuilder('c')->orderBy('c.nom', 'ASC');
                    if ($annee) {
                        $qb->where('c.anneeAcademique = :annee')->setParameter('annee', $annee);
                    }
                    return $qb;
                },
            ])
            ->add('dateInscription', DateType::class, [
                'label'  => "Date d'inscription",
                'widget' => 'single_text',
                'data'   => new \DateTime(),
            ])
            ->add('statut', ChoiceType::class, [
                'label'   => 'Statut',
                'choices' => [
                    'Actif'     => 'actif',
                    'Transféré' => 'transféré',
                    'Exclu'     => 'exclu',
                    'Diplômé'   => 'diplômé',
                ],
                'attr' => ['class' => 'form-select'],
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
            'data_class'    => Inscription::class,
            'annee_courante' => null,
        ]);
        $resolver->setAllowedTypes('annee_courante', [AnneeAcademique::class, 'null']);
    }
}
