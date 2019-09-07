<?php

namespace App\Form\Project;

use App\Entity\User;
use App\Entity\Project;
use App\Form\ApplicationType;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'mapped'        => false,
                'required'      => false,
                'query_builder' => function(UserRepository $rep) {
                    return $rep->findCurrentPromoType();
                }
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
