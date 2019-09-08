<?php

namespace App\Form\Promotion;

use App\Entity\User;
use App\Entity\Promotion;
use App\Form\ApplicationType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PromotionType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label',    TextType::class, $this->getConfiguration('Label de la promotion', 'Promo 1.1'))
            ->add('nickname', TextType::class, $this->getConfiguration('Surnom de la promotion','Les simploniens',['required'=>false]))
            ->add('startAt',  DateType::class, [
                'label'    => 'Débutée le',
                'widget'   => 'single_text',
                'required' => false
            ])
            ->add('endAt',    DateType::class, [
                'label'    => 'Terminée le',
                'widget'   => 'single_text',
                'required' => false
            ])
            ->add('moderators',EntityType::class, [
                'class'         => User::class,
                'placeholder'   => 'Liste des modérateurs de la promotion',
                'required'      => false,
                'multiple'      => true,
                'mapped'        => false,
                'query_builder' => function (UserRepository $ur) {
                    return $ur->getAllUserByRole();
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }
}
