<?php

namespace App\Form\Help;

use App\Entity\Help;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class HelpType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('link',        TextType::class,     $this->getConfiguration('Lien du partage'))
            ->add('description', TextareaType::class, $this->getConfiguration('Description du partage','',['required' => false]))
            ->add('title',       TextType::class,     $this->getConfiguration('Titre du lien'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Help::class,
        ]);
    }
}
