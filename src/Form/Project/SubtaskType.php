<?php

namespace App\Form\Project;

use App\Entity\Subtask;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class SubtaskType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',   TextType::class,     $this->getConfiguration(null, 'Titre de la sous-tache'))
            ->add('done',    CheckboxType::class, $this->getConfiguration('✓',null,[
                'attr'       => array('class' => 'custom-control-input'),
                'label_attr' => array('class' => 'custom-control-label'),
                'required'   => false
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Subtask::class,
        ]);
    }
}
