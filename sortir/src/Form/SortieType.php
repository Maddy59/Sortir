<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\User;
use App\Entity\Ville;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, [
                'label'=> 'Nom de la sortie :'
            ])
            ->add('dateHeureDebut', DateTimeType::class,[
                'html5'=>true,
                'widget'=> 'single_text',
                'label'=> 'Date et heure de la sortie :'
            ])
            ->add('dateLimiteInscription', DateType::class,[
                'html5'=>true,
                'widget'=>'single_text',
                'label'=>'Date limite d\'inscription'
            ])
            ->add('nbInscriptionsMax', null, [
                'label'=>'Nombre de places :'
            ])
            ->add('duree', IntegerType::class, [
                'label'=>'Durée :'
            ])
            ->add('infosSortie', null, [
                'required' => false,
                'label' => 'Description et infos'
            ])
            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'placeholder'  => 'Choisissez le campus',
                'choice_label' => 'nom',
                'multiple'=>false
            ])
            ->add('ville', EntityType::class, [
                'class' => Ville::class,
                'placeholder'  => 'Choisissez la ville',
                'choice_label' => 'nom',
                'mapped' => false,
                'multiple'=>false
            ])
            ->add('lieu', EntityType::class, [
                'class' => Lieu::class,
                'choice_label' => 'nom',
                'placeholder'  => 'Choisissez le lieu',
                'mapped' => true,
                'multiple'=>false
            ])
            ->add('enregistrer', SubmitType::class, ['label'=>'Enregistrer'])
            ->add('publier', SubmitType::class, ['label'=>'Publier la sortie'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
