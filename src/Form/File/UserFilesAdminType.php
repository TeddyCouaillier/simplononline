<?php

namespace App\Form\File;

use App\Entity\User;
use App\Entity\Files;
use App\Entity\UserFiles;
use App\Form\File\FilesType;
use App\Form\ApplicationType;
use App\Form\File\UserFilesType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class UserFilesAdminType extends UserFilesType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'fullname',
                'label' => 'Destinataire'
            ])
        ;
    }
}
