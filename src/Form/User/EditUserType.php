<?php

namespace App\Form\User;

use App\Form\User\UserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class EditUserType extends UserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('tel'      , TextType::class, $this->getConfiguration("N° de téléphone",false,['required'=>false]))
            ->add('zipcode'  , TextType::class, $this->getConfiguration("Code postal",false,['required'=>false]))
            ->add('city'     , TextType::class, $this->getConfiguration("Ville",false,['required'=>false]))
            ->add('website'  , TextType::class, $this->getConfiguration("Site internet",false,['required'=>false]))
            ->add('github'   , TextType::class, $this->getConfiguration("Lien github",false,['required'=>false]))
        ;
    }
}
