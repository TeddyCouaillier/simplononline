<?php

namespace App\Form\Other;

use App\Entity\Deadline;
use App\Form\ApplicationType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeadlineType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('endAt', DateType::class,     $this->getConfiguration('Date finale',null, [ 'widget'   => 'single_text']))
            ->add('task',  TextareaType::class, $this->getConfiguration('Intitulé de la tâche','Ex. finir le projet, arreter de boire, etc.'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Deadline::class,
            'allow_extra_fields' => true
        ]);
    }
}
