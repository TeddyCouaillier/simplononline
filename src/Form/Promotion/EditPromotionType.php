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

class EditPromotionType extends ApplicationType
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
            "allow_extra_fields" => true
        ]);
    }
}
