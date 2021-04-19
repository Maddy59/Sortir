<?php

namespace App\Form;

use App\Entity\Sortie;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnulerSortieForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('nom', null,
                [
                    'attr' => ['readonly' => true],
                ])
            ->add('dateHeureDebut', DateTimeType::class,
                [
                    'widget' => 'single_text',
                    'attr' => ['readonly' => true],
                    'label' => 'Date',
                ])
            ->add('campus', TextType::class, [
                'attr' => ['readonly' => true],
            ])
            ->add('lieu', TextType::class, [
                'attr' => ['readonly' => true],
            ])
            ->add('motif', null, [
                'label' => 'Motif',
                'attr' => ['placeholder' => 'Max : 500 mots'],
            ])
            ->add('Enregistrer', SubmitType::class)
            ->add('Annuler', ButtonType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }

}
