<?php

namespace App\Form\TrainingCourse;

use App\Form\ApplicationType;
use App\Entity\TrainingCourse;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class AdminTrainingCourseType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('society', TextType::class,     $this->getConfiguration(null,'Nom de la société'))
            ->add('place',   TextType::class,     $this->getConfiguration(null,'Lieu du stage'))
            ->add('project', TextareaType::class, $this->getConfiguration('Détail du stage','Projet pendant le stage, entretien à telle date, autres ...', ['required' => false]))
            ->add('number',  IntegerType::class,  $this->getConfiguration(null,'0',['required' => false]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrainingCourse::class,
        ]);
    }
}
