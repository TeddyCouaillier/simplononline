<?php

namespace App\Form\Project;

use App\Entity\Project;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditProjectType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',       TextType::class,     $this->getConfiguration("Titre du projet"))
            ->add('description', TextareaType::class, $this->getConfiguration("Description"))
            ->add('github',      TextType::class,     $this->getConfiguration("Lien GitHub",null,['required'=> false]))
            ->add('website',     TextType::class,     $this->getConfiguration("Site internet",null,['required'=> false]))
            ->add('completed',   CheckboxType::class, $this->getConfiguration('Terminé',null,[
                'attr'       => array('class' => 'switch_base'),
                // 'label_attr' => array('class' => 'custom-control-label'),
                'required'   => false
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
