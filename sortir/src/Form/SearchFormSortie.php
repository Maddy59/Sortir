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
use Symfony\Component\Validator\Constraints\Date;

class SearchFormSortie extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'required' => false,
                'class' => Campus::class,

            ])
            ->add('recherche', TextType::class, [
                'label' => 'Le nom de la sortie contient:',
                'required' => false,
                'attr' => ['placeholder' => 'Recherche'],
                'empty_data' => '',
            ])
//            ->add('dateDebut', DateType::class, [
//                'html5' => true,
//                'widget' => 'single_text',
//                'required' => false,
//            ])
//            ->add('dateCloture', DateType::class, [
//                'html5' => true,
//                'widget' => 'single_text',
//                'required' => false,
//                'empty_data' => new Date(),
//            ])
            ->add('categories', ChoiceType::class, [
                'label' => false,
                'required' => false,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Sortie dont je suis l\'organisateur/trice' => 0,
                    'Sortie auxquelles je suis isncrit/e' => 1,
                    'Sortie auxquelles je ne suis pas inscrit/e' => 2,
                    'Sorties passées' => 3]
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