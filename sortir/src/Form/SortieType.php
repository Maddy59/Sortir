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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', null, [
                'label'=> 'Nom de la sortie :'
            ])->add('organisateur', EntityType::class, [
                'class' => User::class,
                'label'=> 'Nom de l\'organisateur :',
                'choice_label' => 'nom',
                'multiple'=>false
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
            ->add('duree', null, [
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
                'mapped' => false,
                'multiple'=>false
            ])
        ;
      /* $builder->get('ville')->addEventListener(
          FormEvents::PRE_SUBMIT,
          function (FormEvent $event){
              $form = $event->getForm();
              $form->getParent()->add('lieu', EntityType::class, [
                  'class'=> Lieu::class,
                  'placeholder' => 'Sélectionnez votre lieu',
                  'choices' => $form->getData()->getLieux()
              ]);
          }
        );*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
