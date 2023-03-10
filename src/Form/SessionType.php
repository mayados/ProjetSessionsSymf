<?php

namespace App\Form;

use App\Entity\Session;
use App\Entity\Formateur;
use App\Entity\Formation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class SessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('intitule', TextType::class,[
                'label' => false,
            ])
            ->add('dateDebut', DateType::class, [
                // Pour avoir un mini calendrier à l'affichage
                'widget' => 'single_text',
                'label' => false,
            ])
            ->add('dateFin', DateType::class, [
                // Pour avoir un mini calendrier à l'affichage
                'widget' => 'single_text',
                'label' => false
            ])
            ->add('nbPlaces', IntegerType::class,[
                'label' => false,
            ])
            ->add('referent', EntityType::class, [
                'class' => Formateur::class,
                'placeholder' => '...',
                'required' => false,
                'label' => false,
            ])
            // ->add('formation', EntityType::class, [
            //     'class' => Formation::class,
            // ])
            ->add('submit', SubmitType::class,[
                'label' => 'Envoyer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Session::class,
        ]);
    }
}
