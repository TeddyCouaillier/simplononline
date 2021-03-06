<?php

namespace App\Form\User;

use App\Entity\Role;
use App\Entity\Promotion;
use App\Form\User\UserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class CreateUserType extends UserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('password',  PasswordType::class, $this->getConfiguration(null, "Mot de passe",["required"=>false]))
            ->add('promotion', EntityType::class, [
                'class'        => Promotion::class,
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
