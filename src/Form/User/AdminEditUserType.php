<?php

namespace App\Form\User;

use App\Entity\Role;
use App\Entity\Promotion;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AdminEditUserType extends EditUserType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
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
