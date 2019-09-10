<?php

namespace App\Form;

use App\Entity\Schedule;
use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ScheduleType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('beginAt',   DateTimeType::class, $this->getConfiguration('Date de début', null, ['widget'   => 'single_text']))
            ->add('endAt',     DateTimeType::class, $this->getConfiguration('Date de fin',   null, ['widget'   => 'single_text']))
            ->add('title',     TextareaType::class, $this->getConfiguration('Description', 'Ex. Aller à la piscine, coder sans les mains, etc.'))
            ->add('important', CheckboxType::class, $this->getConfiguration(' ',null,[
                'attr'       => array('class' => 'custom-control-input'),
                'label_attr' => array('class' => 'custom-control-label'),
                'required'   => false
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Schedule::class,
        ]);
    }
}
