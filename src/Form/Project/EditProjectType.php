<?php

namespace App\Form\Project;

use App\Entity\User;
use App\Entity\Project;
use App\Form\ApplicationType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditProjectType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration(null,"Titre du projet"))
            ->add('description', TextareaType::class, $this->getConfiguration(null,"Description du projet"))
            ->add('github', TextType::class, $this->getConfiguration(null,"Lien GitHub du projet",['required'=> false]))
            ->add('website', TextType::class, $this->getConfiguration(null,"Site internet du projet",['required'=> false]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
