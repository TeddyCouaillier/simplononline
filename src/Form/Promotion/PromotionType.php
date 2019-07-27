<?php

namespace App\Form\Promotion;

use App\Entity\Promotion;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class PromotionType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('label',    TextType::class,     $this->getConfiguration('Label de la promotion', 'Promo 1.1'))
            ->add('nickname', TextType::class,     $this->getConfiguration('Surnom de la promotion','Les simploniens',['required'=>false]))
            ->add('current',  CheckboxType::class, $this->getConfiguration('Actuelle',null,[
                'attr'       => array('class' => 'custom-control-input'),
                'label_attr' => array('class' => 'custom-control-label'),
                'required'   => false
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Promotion::class,
        ]);
    }
}
