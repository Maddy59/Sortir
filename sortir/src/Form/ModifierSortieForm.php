<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use App\Entity\Ville;
use App\Form\LieuType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifierSortieForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('nom', null, [
                'label' => 'Nom de la sortie :',
            ])
            ->add('dateHeureDebut', DateTimeType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Date et heure de la sortie :',
            ])
            ->add('dateLimiteInscription', DateType::class, [
                'html5' => true,
                'widget' => 'single_text',
                'label' => 'Date limite d\'inscription :',
            ])
            ->add('nbInscriptionsMax', null, [
                'label' => 'Nombre de places :',
            ])
            ->add('duree', IntegerType::class, [
                'label' => 'Durée (min) :',
            ])
            ->add('infosSortie', null, [
                'required' => false,
                'label' => 'Description et infos',
            ])

            ->add('campus', EntityType::class, [
                'class' => Campus::class,
                'placeholder' => 'Choisissez le campus',
                'choice_label' => 'nom',
                'multiple' => false,
            ])
            ->add('adresse', TextType::class, [
                'mapped' => false,
                'label' => 'Adresse du lieu',
                'required' => true,
            ])
            ->add('lieu', LieuType::class, [
                'label' => false,
                'required' => true,
            ])

            ->add('ville', EntityType::class, [
                'mapped' => false,
                'class' => Ville::class,
                'required' => true,
                'choice_label' => function (Ville $ville) {
                    return $ville->getNom() . ', ' . $ville->getCodePostal();
                },
                'query_builder' => function (EntityRepository $repo) use ($options) {
                    return $repo->createQueryBuilder('e')
                        ->addSelect("(CASE WHEN e = :id THEN 1 ELSE 0 END) AS HIDDEN firstValue")
                        ->setParameter('id', $options['idVille'])
                        ->orderBy('firstValue', 'DESC');
                },
            ])

            ->add('enregistrer', SubmitType::class, ['label' => 'Enregistrer'])
            ->add('publier', SubmitType::class, ['label' => 'Publier la sortie'])
            ->add('supprimer', SubmitType::class, ['label' => 'Supprimer'])
            ->add('Annuler', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {

        $resolver->setDefaults([
            'data_class' => Sortie::class,
            'idVille' => 1,
        ]);
    }
}
