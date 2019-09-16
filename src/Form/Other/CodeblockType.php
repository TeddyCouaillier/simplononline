<?php

namespace App\Form\Other;

use App\Entity\Language;
use App\Entity\Codeblock;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CodeblockType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, $this->getConfiguration(null,"Ex. Code pour un preloader"))
            ->add('language', EntityType::class, $this->getConfiguration('Langage principal du jeu', null, [
                'class'  =>   Language::class
            ]))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Codeblock::class,
        ]);
    }
}
