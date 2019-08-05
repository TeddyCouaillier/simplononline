<?php

namespace App\Form\Project;

use App\Entity\Task;
use App\Entity\User;
use App\Form\ApplicationType;
use App\Form\Project\SubtaskType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EditTaskType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',       TextType::class,     $this->getConfiguration(null,"Intitulé de la tache"))
            ->add('description', TextareaType::class, $this->getConfiguration(null,"Description de la tache",['required'=>false]))
            ->add('type',        ChoiceType::class,[
                'label'       => 'Etat de la tâche',
                'choices'     => Task::TYPE,
                'placeholder' => "Veuillez choisir un état"
            ])
            ->add('subtasks', CollectionType::class, [
                'entry_type'   => SubtaskType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'attr' => [
                    'class' => 'edit-task',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
