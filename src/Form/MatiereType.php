<?php
namespace App\Form;

use App\Entity\Matiere;
use App\Enum\Niveau;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MatiereType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, ['label' => 'Nom de la matière', 'attr' => ['class' => 'form-input']])
            ->add('code', TextType::class, ['label' => 'Code', 'required' => false, 'attr' => ['class' => 'form-input', 'placeholder' => 'ex: MATH-P4']])
            ->add('coefficient', NumberType::class, ['label' => 'Coefficient', 'attr' => ['class' => 'form-input', 'step' => '0.5', 'min' => '0.5']])
            ->add('niveau', EnumType::class, [
                'class'        => Niveau::class,
                'label'        => 'Niveau scolaire',
                'choice_label' => fn(Niveau $n) => $n->label(),
                'attr'         => ['class' => 'form-select'],
            ])
            ->add('description', TextareaType::class, ['label' => 'Description', 'required' => false, 'attr' => ['class' => 'form-textarea', 'rows' => 3]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Matiere::class]);
    }
}
