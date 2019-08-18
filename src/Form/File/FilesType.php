<?php

namespace App\Form\File;

use App\Entity\User;
use App\Entity\Files;
use App\Form\ApplicationType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class FilesType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',      FileType::class,['data_class' => null])
            ->add('title',     TextType::class,$this->getConfiguration('Titre de votre document','Max. 50 caractères'))
            ->add('important', CheckboxType::class, [
                'mapped'     => false,
                'attr'       => array('class' => "custom-control-input"),
                'label_attr' => array('class' => 'custom-control-label'),
                'required'   => false
            ])
            ->add('receiver',  EntityType::class, [
                'class'         => User::class,
                'choice_label'  => 'fullname',
                'label'         => 'Destinataire',
                'multiple'      => true,
                'mapped'        => false,
                'query_builder' => function(UserRepository $rep) {
                    return $rep->getAllUserRoleByCurrentPromo();
                }

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Files::class,
        ]);
    }
}
