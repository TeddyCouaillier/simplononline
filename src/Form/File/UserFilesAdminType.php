<?php

namespace App\Form\File;

use App\Entity\User;
use App\Form\File\UserFilesType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UserFilesAdminType extends UserFilesType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('receiver', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullname',
                'label' => 'Destinataire'
            ])
        ;
    }
}
