<?php

namespace App\Form\Project;

use App\Form\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\Correction;
use App\Entity\Project;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AddCorrectionType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, $this->getConfiguration("Contenu du corrigé"))
            ->add('project', EntityType::class, [
                'class'        => Project::class,
                'choice_label' => 'title',
                'placeholder'  => 'Choisir le projet à corriger'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Correction::class,
        ]);
    }
}
