<?php

namespace App\Form;

use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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
            ->add('campus')
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_value' => 'nom',
                'mapped' => false,
                'attr' => ['readonly' => true],
            ])
            ->add('motif', null, [
                'label' => 'Motif',
                'attr' => ['placeholder' => 'Max : 500 mots'],
            ])
            ->add('Enregistrer', SubmitType::class)
            ->add('Annuler', SubmitType::class);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }

}
