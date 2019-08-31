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
use App\Form\Data\EditUserDataType;
use App\Form\Skill\EditUserSkillsType;
use App\Repository\TrainingCourseRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\TrainingCourse\TrainingCourseUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * Create an user
     * @Route("/create-user", name="create")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
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
                if($role !== null){
                    $role->addUser($user);
                    $user->setPromotion(null);
                }
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
            return $this->redirectToRoute("user_show",['slug' => $user->getSlug()]);
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit the current user
     * @Route("/{slug}/edit", name="edit")
     * @param User          $user
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editUserAccount(User $user, Request $request, ObjectManager $manager){
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() != $user){
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

                // $role_id = $request->request->get('edit_user')['userRoles'];
                // if($role_id !== null){
                //     $reprole = $this->getDoctrine()->getRepository(Role::Class);
                //     $role = $reprole->find($role_id);
                //     $role->addUser($user);
                //     $user->setPromotion(null);
                // }

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'L\'utilisateur a bien été mis à jour.'
                );
                return $this->redirectToRoute('user_show', ['slug'=> $user->getSlug()]);
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Delete a specific user
     * @Route("/{slug}/delete", name="delete")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
     * @param User          $user
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteUser(User $user, ObjectManager $manager)
    {
        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'success',
            'L\'utilisateur a bien été supprimé.'
        );

        return $this->redirectToRoute('admin_all_users', ['slug' => 'all']);
    }

    // -----------------------------------------------------
    // -- Skill section
    // -----------------------------------------------------

    /**
     * Edit user's skill
     * @Route("/{slug}/competences", name="edit_skills")
     * @IsGranted("ROLE_FORMER")
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
            $notif = $user->createSenderNotif(Notification::SKILL, $user->getSlug());
            $user->createUserNotif($notif);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les compétences ont bien été modifiées'
            );

            return $this->redirectToRoute("admin_all_users", ['slug' => 'all']);
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
     * @Route("/{slug}/donnees", name="data")
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

    /**
     * Edit the user's datas
     * @Route("/{slug}/donnees/edit", name="data_edit")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
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
            return $this->redirectToRoute("admin_all_users");
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
     * @Route("/{slug}/stages/edit", name="edit_training")
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
     * @Route("/{slug}/stages/delete/all", name="delete_all_trainings")
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
     * @Route("/{slug}/stages/delete/{training_id}", name="delete_training")
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
    // -- Project section
    // -----------------------------------------------------

    /**
     * Show the user's projects
     * @Route("/{slug}/projets", name="show_projects")
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

        return $this->redirectToRoute('user_show_notif',['slug' => $this->getUser()->getSlug()]);
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

        return $this->redirectToRoute('user_show_notif',['slug' => $this->getUser()->getSlug()]);
    }

    /**
     * Show all user's notifications
     * @Route("/{slug}/notification", name="show_notif")
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
     * @Route("/account", name="account_show")
     * @Route("/{slug}", name="show")
     * @param User          $user
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function showUser(User $user = null, Request $request, ObjectManager $manager)
    {
        if($user == null){
            $user = $this->getUser();
        }
        if($request->query->get('seen') != null){
            $unotif = $this->getDoctrine()->getRepository(UserNotif::class)->find($request->query->get('seen'));
            if($unotif != null){
                $unotif->setSeen(true);
                $manager->persist($unotif);
                $manager->flush();
            }
        }

        if($user != $this->getUser()){
            return $this->render('user/show.html.twig', [
                'user' => $user
            ]);
        }

        return $this->render('user/account.html.twig', [
            'user' => $user,
            'date' => new \DateTime()
        ]);
    }
}
