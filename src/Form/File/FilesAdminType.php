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

class FilesAdminType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',    TextType::class,$this->getConfiguration('Titre de votre document','Max. 50 caractères'))
            ->add('name',     FileType::class,[
                'data_class'    => null,
                'label'         => 'Taille maximum: 5mo'
            ])
            ->add('receiver', EntityType::class, [
                'class'         => User::class,
                'label'         => 'Destinataire',
                'multiple'      => true,
                'mapped'        => false,
                'query_builder' => function(UserRepository $rep) {
                    return $rep->findCurrentPromoType();
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
