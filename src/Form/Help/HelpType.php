<?php

namespace App\Form\Help;

use App\Entity\Help;
use App\Entity\Language;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class HelpType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('link',        TextType::class,     $this->getConfiguration('Lien du partage'))
            ->add('title',       TextType::class,     $this->getConfiguration('Titre du lien'))
            ->add('language',   EntityType::class, [
                'class'        => Language::class,
                'choice_label' => 'label',
                'label'        => 'Langage',
                'required'     => false,
                'placeholder'  => 'Aucun'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Help::class,
        ]);
    }
}
