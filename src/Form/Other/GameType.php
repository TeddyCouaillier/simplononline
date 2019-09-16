<?php

namespace App\Form\Other;

use App\Entity\Game;
use App\Entity\Language;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class GameType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',    TextType::class,   $this->getConfiguration('','Titre de votre jeu'))
            ->add('link',     UrlType::class,    $this->getConfiguration('','http://lien-du-jeux.com'))
            ->add('github',   UrlType::class,    $this->getConfiguration('','http://lien-github-du-jeux.com'))
            ->add('language', EntityType::class, $this->getConfiguration('Langage principal du jeu', null, [
                'class'  =>   Language::class
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Game::class,
        ]);
    }
}
