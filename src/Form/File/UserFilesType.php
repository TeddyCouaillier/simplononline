<?php

namespace App\Form\File;

use App\Entity\Files;
use App\Entity\UserFiles;
use App\Form\File\FilesType;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserFilesType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FilesType::class, [
                'data_class' => Files::class
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UserFiles::class,
            'validation_groups' => ['upload']
        ]);
    }
}
