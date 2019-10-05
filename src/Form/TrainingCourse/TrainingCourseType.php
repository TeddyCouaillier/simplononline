<?php

namespace App\Form\TrainingCourse;

use App\Form\ApplicationType;
use App\Entity\TrainingCourse;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrainingCourseType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('society', TextType::class,     $this->getConfiguration(null,'Nom de la société'))
            ->add('place',   TextType::class,     $this->getConfiguration(null,'Lieu du stage'))
            ->add('project', TextareaType::class, $this->getConfiguration('', '500 caractères maximum', ['required' => false]))
            ->add('status',  ChoiceType::class, [
                'label'       => 'Etat du stage',
                'choices'     => TrainingCourse::STATUS,
                'placeholder' => "Veuillez choisir un état"
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrainingCourse::class,
        ]);
    }
}
