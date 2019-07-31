<?php

namespace App\Form\Skill;

use App\Entity\User;
use App\Entity\UserSkills;
use App\Form\ApplicationType;
use App\Form\Skill\UserSkillsType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class EditUserSkillsType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userSkills', CollectionType::class, [
                'entry_type'  => UserSkillsType::class,
                'constraints' => array(new Valid())
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
