<?php

namespace App\Form\Project;

use App\Entity\Project;
use App\Entity\Correction;
use App\Form\ApplicationType;
use App\Repository\ProjectRepository;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AddCorrectionType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', TextareaType::class, $this->getConfiguration("Contenu du corrigé"))
            ->add('project', EntityType::class, [
                'class'        => Project::class,
                'placeholder'  => 'Choisir le projet à corriger (promo actuelle)',
                'query_builder' => function(ProjectRepository $rep) {
                    return $rep->getAllProjectByCurrentPromo();
                }
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
