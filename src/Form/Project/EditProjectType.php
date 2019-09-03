<?php

namespace App\Form\Project;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\Language;
use App\Form\ApplicationType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormEvent;
use App\Repository\LanguageRepository;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
                'required'   => false,
                'attr'          => array('class' => "custom-control-input"),
                'label_attr'    => array('class' => 'custom-control-label'),
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
            "allow_extra_fields" => true
        ]);
    }
}
