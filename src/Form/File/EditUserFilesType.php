<?php

namespace App\Form\File;

use App\Entity\User;
use App\Entity\UserFiles;
use App\Form\ApplicationType;
use App\Form\File\UserFilesType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditUserFilesType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userFiles', UserFilesType::class, [
                'data_class' => UserFiles::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
