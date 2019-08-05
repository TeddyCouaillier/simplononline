<?php

namespace App\Form\TrainingCourse;

use App\Entity\User;
use App\Form\ApplicationType;
use App\Form\TrainingCourse\TrainingCourseType;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\TrainingCourse\AdminTrainingCourseType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdminTrainingCourseUserType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('trainingCourse', CollectionType::class, [
                'entry_type'   => AdminTrainingCourseType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => [
                    'class' => 'training',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
