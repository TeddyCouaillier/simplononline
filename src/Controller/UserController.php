<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\TrainingCourse;
use App\Repository\ProjectRepository;
use App\Repository\TrainingCourseRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TrainingCourse\TrainingCourseUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route("", name="user_")
 */
class UserController extends AbstractController
{
    // -----------------------------------------------------
    // -- Project section
    // -----------------------------------------------------
    /**
     * Show the user's projects
     * @Route("/{slug}/projets/{page<\d+>?1}", name="show_projects")
     * @param integer           $page
     * @param User              $user
     * @param ProjectRepository $rep
     * @return Response
     */
    public function showUserProjects(int $page, User $user, ProjectRepository $rep)
    {
        $limit = 20;
        $offset = $page * $limit - $limit;
        $projects = $rep->findAllByUserLimit($user, $limit, $offset);
        $total = count($rep->findAllByUser($user));
        $pages = ceil($total / $limit);

        return $this->render('project/all.html.twig', [
            'user'     => $user,
            'page'     => $page,
            'pages'    => $pages,
            'projects' => $projects
        ]);
    }

    // -----------------------------------------------------
    // -- Training course section
    // -----------------------------------------------------
    /**
     * Show the specific user's training courses by the same user
     * Show all users training courses by the admin
     * Show the training courses proposed by the admin for all users
     * @Route("/{slug}/stages", name="show_training")
     * @param User                      $user
     * @param TrainingCourseRepository  $rep
     * @return Response
     */
    public function showTraining(User $user, TrainingCourseRepository $rep)
    {
        return $this->render('training_course/show.html.twig', [
            'user'          => $user,
            'trainingAdmin' => $rep->findAllTrainingAdmin(),
            'trainings'     => $rep->findAllTraining()
        ]);
    }

    /**
     * Edit the specific user's training courses (add, remove & edit training course)
     * @Route("/{slug}/stages/modifier", name="edit_training")
     * @param User          $user
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editTrainingCourse(User $user, Request $request, ObjectManager $manager)
    {
        if($this->getUser() != $user) {
            throw new AccessDeniedException();
        }
        $form = $this->createForm(TrainingCourseUserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les modifications ont bien été enregistrées.'
            );
            return $this->redirectToRoute('user_show_training', ['slug'=> $user->getSlug()]);
        }

        return $this->render('training_course/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete all trainings courses posted by a specific user
     * @Route("/{slug}/stages/tout-supprimer", name="delete_all_trainings")
     * @param User          $user
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteAllTrainingCourse(User $user, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() != $user){
            throw new AccessDeniedHttpException();
        }

        foreach($user->getTrainingCourse() as $training){
            $manager->remove($training);
        }
        $manager->flush();

        $this->addFlash(
            'success',
            'Les stages ont bien été supprimés.'
        );

        return $this->redirectToRoute('user_show_training',['slug'=> $this->getUser()->getSlug()]);
    }

    /**
     * Delete a specific training course posted by a specific user
     * @Route("/{slug}/stages/{training_id}/supprimer", name="delete_training")
     * @Entity("training", expr="repository.find(training_id)")
     * @param User           $user
     * @param TrainingCourse $training
     * @param ObjectManager  $manager
     * @return Response
     */
    public function deleteTrainingCourse(User $user, TrainingCourse $training, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() != $user){
            throw new AccessDeniedHttpException();
        }

        $manager->remove($training);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le stage a bien été supprimé.'
        );

        return $this->redirectToRoute('user_show_training',['slug'=> $this->getUser()->getSlug()]);
    }

    // -----------------------------------------------------
    // -- Data section
    // -----------------------------------------------------
    /**
     * Show the user's data
     * @Route("/donnees/{slug}", name="data")
     * @param User $user
     * @return Response
     */
    public function showData(User $user)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() != $user){
            throw new AccessDeniedHttpException();
        }
        return $this->render('data/show.html.twig', [
            'user' => $user
        ]);
    }
}
