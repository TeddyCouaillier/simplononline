<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Form\TrainingCourse\AdminTrainingCourseUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;

class TrainingCourseController extends AbstractController
{
    /**
     * Add many training courses by the admin
     * @Route("stages/ajouter", name="training_add")
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function addTrainingCourse(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();
        $form = $this->createForm(AdminTrainingCourseUserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les modifications ont bien été enregistrées.'
            );
            return $this->redirectToRoute('user_show_training', ['id'=> $user->getId()]);
        }

        return $this->render('training_course/admin_edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}
