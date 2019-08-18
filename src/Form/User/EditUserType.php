<?php

namespace App\Form\User;

use App\Entity\Role;
use App\Entity\Promotion;
use App\Form\User\UserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
            ->add('avatar'   , FileType::class, [ 'required' => false,'data_class' => null])
            ->add('promotion', EntityType::class, [
                'class'        => Promotion::class,
                'choice_label' => 'label',
                'required'     => false,
                'placeholder'  => 'Aucune'
            ])
            ->add('userRoles', EntityType::class, [
                'class'       => Role::class,
                'required'    => false,
                'mapped'      => false,
                'placeholder' => 'Apprenant'
            ])
        ;
    }
}
