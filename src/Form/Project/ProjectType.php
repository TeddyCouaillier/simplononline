<?php

namespace App\Form\Project;

use App\Entity\User;
use App\Entity\Project;
use App\Entity\Language;
use App\Form\ApplicationType;
use App\Repository\UserRepository;
use App\Repository\LanguageRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProjectType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',       TextType::class,     $this->getConfiguration(null,"Titre du projet"))
            ->add('description', TextareaType::class, $this->getConfiguration(null,"Description du projet",['required'=> false]))
            ->add('github',      TextType::class,     $this->getConfiguration(null,"Lien GitHub du projet",['required'=> false]))
            ->add('website',     TextType::class,     $this->getConfiguration(null,"Site internet du projet",['required'=> false]))
            ->add('users',       EntityType::class, [
                'class'         => User::class,
                'placeholder'   => 'Liste des apprenants du projet',
                'multiple'      => true,
                'mapped'        => false,
                'required'      => false,
                'query_builder' => function(UserRepository $rep) {
                    return $rep->findCurrentPromoType();
                }
            ])
            ->add('languages',   EntityType::class, [
                'class'         => Language::class,
                'choice_label'  => 'label',
                'placeholder'   => 'Liste des apprenants du projet',
                'multiple'      => true,
                'mapped'        => false,
                'query_builder' => function (LanguageRepository $er) {
                    return $er->getAllLanguages();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
