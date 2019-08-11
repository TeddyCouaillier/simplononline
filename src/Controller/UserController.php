<?php

namespace App\Controller;

use App\Entity\Data;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Skills;
use App\Entity\UserNotif;
use App\Entity\Notification;
use App\Entity\TrainingCourse;
use App\Form\User\EditUserType;
use App\Form\User\CreateUserType;
use App\Repository\UserRepository;
use App\Form\Data\EditUserDataType;
use App\Form\Skill\EditUserSkillsType;
use App\Repository\TrainingCourseRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TrainingCourse\TrainingCourseUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * Create an user
     * @Route("/create", name="create")
     * @IsGranted("ROLE_ADMIN")
     * @param Request                      $request
     * @param ObjectManager                $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function createUser(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $role_id = $request->request->get('create_user')['userRoles'];
            if($role_id !== null){
                $reprole = $this->getDoctrine()->getRepository(Role::Class);
                $role = $reprole->find($role_id);
                $role->addUser($user);
                $user->setPromotion(null);
            }

            $user->setPassword($encoder->encodePassword($user, 'test'));
            $user->setAvatar('avatar.png');

            $skills = $this->getDoctrine()->getRepository(Skills::class)->findAll();
            $datas  = $this->getDoctrine()->getRepository(Data::class)->findAll();

            $user->initializeSkills($skills);
            $user->initializeDatas($datas);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'L\'utilisateur a bien été créé.'
            );
            return $this->redirectToRoute("user_show",['id' => $user->getId()]);
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit the current user
     * @Route("/edit/{id}", name="edit")
     * @param User          $user
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editUserAccount(User $user, Request $request, ObjectManager $manager){
        if(!$this->getUser()->checkRole(User::ADMIN) && $this->getUser() != $user){
            throw new AccessDeniedHttpException();
        }
        $imageName = "";

        $currentAvatar = $user->getAvatar();
        if(!empty($currentAvatar)){
            $imageName = $user->getAvatar();
        }
        $form = $this->createForm(EditUserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted()){
            if(!$form->isValid()){
                $user->setAvatar($imageName);
            } else {
                $image = $form->get('avatar')->getData();
                if($image != NULL)
                {
                    $imageName = $user->getAvatarName().'.'.$image->guessExtension();
                    $image->move(
                        $this->getParameter('image_directory'),
                        $imageName
                    );
                    $user->setAvatar($imageName);
                } else {
                    $user->setAvatar($imageName);
                }

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'L\'utilisateur a bien été mis à jour.'
                );
                return $this->redirectToRoute('user_show', ['id'=> $user->getId()]);
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Show all users
     * @Route("/all", name="all")
     * @param UserRepository $rep
     * @return Response
     */
    public function allUsers(UserRepository $rep)
    {
        return $this->render('user/all.html.twig', [
            'users' => $rep->findAll()
        ]);
    }

    /**
     * Delete a specific user
     * @Route("/{id}/delete", name="delete")
     * @IsGranted("ROLE_ADMIN")
     * @param User          $user
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteUser(User $user, ObjectManager $manager)
    {
        $manager->remove($user);
        $manager->flush();
        return $this->redirectToRoute('user_all');
    }

    // -----------------------------------------------------
    // -- Skill section
    // -----------------------------------------------------

    /**
     * Edit user's skill
     * @Route("/{id}/competences", name="edit_skills")
     * @IsGranted("ROLE_ADMIN")
     * @param User $user
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editSkillsUser(User $user, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(EditUserSkillsType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $notif = $user->createSenderNotif(Notification::SKILL, $user->getId());
            $user->createUserNotif($notif);

            $manager->persist($user);
            $manager->flush();

            return $this->redirectToRoute("user_show",['id' => $user->getId()]);
        }
        return $this->render('skill/edit_skills.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    // -----------------------------------------------------
    // -- Data section
    // -----------------------------------------------------

    /**
     * Show the user's data
     * @Route("/{id}/donnees", name="data")
     * @param User $user
     * @return Response
     */
    public function showData(User $user)
    {
        if(!$this->getUser()->checkRole(User::ADMIN) && $this->getUser() != $user){
            throw new AccessDeniedHttpException();
        }
        return $this->render('data/show.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Edit the user's datas
     * @Route("/{id}/donnees/edit", name="data_edit")
     * @IsGranted("ROLE_ADMIN")
     * @param User          $user
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editData(User $user, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(EditUserDataType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les données ont bien été modifiées.'
            );
            return $this->redirectToRoute("user_data",['id' => $user->getId()]);
        }
        return $this->render('data/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    // -----------------------------------------------------
    // -- Training course section
    // -----------------------------------------------------

    /**
     * Show the specific user's training courses by the same user
     * Show all users training courses by the admin
     * Show the training courses proposed by the admin for all users
     * @Route("/{id}/stages", name="show_training")
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
     * @Route("/{id}/stages/edit", name="edit_training")
     * @param User          $user
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editTrainingCourse(User $user, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(TrainingCourseUserType::class, $user);
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

        return $this->render('training_course/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete a specific training course posted by a specific user
     * @Route("/{id}/stages/delete/{training_id}", name="delete_training")
     * @Entity("training", expr="repository.find(training_id)")
     * @param User           $user
     * @param TrainingCourse $training
     * @param ObjectManager  $manager
     * @return Response
     */
    public function deleteTrainingCourse(User $user, TrainingCourse $training, ObjectManager $manager)
    {
        $manager->remove($training);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le stage a bien été supprimé.'
        );

        return $this->redirectToRoute('user_show_training',['id'=> $this->getUser()->getId()]);
    }

    // -----------------------------------------------------
    // -- Project section
    // -----------------------------------------------------

    /**
     * Show the user's projects
     * @Route("/{id}/projets", name="show_projects")
     * @param User $user
     * @return Response
     */
    public function showUserProjects(User $user)
    {
        return $this->render('project/all.html.twig', [
            'user' => $user
        ]);
    }

    // -----------------------------------------------------
    // -- Notification section
    // -----------------------------------------------------

    /**
     * Delete a specific user's notification
     * @Route("/notification/{id}/delete", name="delete_notif")
     * @param UserNotif     $unotif
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteUserNotif(UserNotif $unotif, ObjectManager $manager)
    {
        $manager->remove($unotif);
        $manager->flush();

        $this->addFlash(
            'success',
            'La notification a bien été supprimée.'
        );

        return $this->redirectToRoute('user_show_notif',['id' => $this->getUser()->getId()]);
    }

    /**
     * Delete all user's notifications
     * @Route("/notification/delete_all", name="delete_all_notif")
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteAllNotif(ObjectManager $manager)
    {
        foreach($this->getUser()->getNotifReceived() as $unotif){
            $manager->remove($unotif);
        }
        $manager->flush();

        $this->addFlash(
            'success',
            'Les notifications ont bien été supprimées.'
        );

        return $this->redirectToRoute('user_show_notif',['id' => $this->getUser()->getId()]);
    }

    /**
     * Show all user's notifications
     * @Route("/{id}/notification", name="show_notif")
     * @param User $user
     * @return Response
     */
    public function showUserNotif(User $user)
    {
        return $this->render('notification/show.html.twig',[
            'user' => $user
        ]);
    }


    /**
     * Show a specific user
     * @Route("/{id}", name="show")
     * @param User          $user
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function showUser(User $user, Request $request, ObjectManager $manager)
    {
        if($request->query->get('seen') != null){
            $unotif = $this->getDoctrine()->getRepository(UserNotif::class)->find($request->query->get('seen'));
            if($unotif != null){
                $unotif->setSeen(true);
                $manager->persist($unotif);
                $manager->flush();
            }
        }

        return $this->render('user/show.html.twig', [
            'user' => $user,
            'date' => new \DateTime()
        ]);
    }
}
