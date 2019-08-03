<?php

namespace App\Form\Project;

use App\Entity\Task;
use App\Entity\User;
use App\Form\ApplicationType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TaskType extends ApplicationType
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
            ->addEventListener(FormEvents::POST_SET_DATA, function (FormEvent $event) {
                $task = $event->getData();
                $event->getForm()->add('users', EntityType::class, [
                    'class'         => User::class,
                    'choice_label'  => 'fullname',
                    'multiple'      => true,
                    'mapped'        => false,
                    'query_builder' => function (UserRepository $rep) use ($task) {
                        return $rep->findAllByProject($task->getProject());
                    }
                ]);
            });
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
