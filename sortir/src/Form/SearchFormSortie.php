<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchFormSortie extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('campus', EntityType::class, [
                'required' => false,
                'class' => Campus::class,
                'placeholder' => 'Tous',

            ])
            ->add('recherche', TextType::class, [
                'required' => false,
                'attr' => ['placeholder' => 'Nom de la sortie'],
            ])
            ->add('dateDebut', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('dateFin', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('categories', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Sortie(s) dont je suis l\'organisateur(trice)' => 'organisateur',
                    'Sortie(s) auxquelle(s) je suis inscrit(e)' => 'inscrit',
                    'Sortie(s) auxquelle(s) je ne suis pas inscrit(e)' => 'non-inscrit',
                    'Sortie(s) passée(s)' => 'passes'],

            ],
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
        ]);
    }

}
