<?php

namespace App\Form\Project;

use App\Entity\Language;
use App\Entity\Project;
use App\Entity\User;
use App\Form\ApplicationType;
use App\Repository\LanguageRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class EditProjectUserType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $em = $this->getDoctrine()->getEntityManager();
        $builder
            ->add('users',       EntityType::class, [
                'class'         => User::class,
                'choice_label'  => 'fullname',
                'placeholder'   => 'Liste des apprenants du projet',
                'multiple'      => true,
                'expanded'      => true,
                // 'mapped'        => false,
                'required'      => false,
                'query_builder' => function(UserRepository $rep) {
                    return $rep->findCurrentPromoType();
                }
                // 'data' => $em->()
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
