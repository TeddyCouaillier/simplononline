<?php

namespace App\Controller\Admin;

use App\Entity\Data;
use App\Entity\Help;
use App\Entity\Role;
use App\Entity\Task;
use App\Entity\User;
use App\Entity\Skills;
use App\Entity\Project;
use App\Entity\Language;
use App\Entity\UserData;
use App\Entity\Promotion;
use App\Entity\Correction;
use App\Form\Data\DataType;
use App\Form\Help\HelpType;
use App\Entity\Notification;
use App\Form\Skill\SkillType;
use App\Form\Project\TaskType;
use App\Repository\HelpRepository;
use App\Repository\UserRepository;
use App\Form\Project\CorrectionType;
use App\Form\User\AdminEditUserType;
use App\Form\Project\EditProjectType;
use App\Repository\PromotionRepository;
use App\Form\Promotion\EditPromotionType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin_")
 */
class AdminEditController extends AbstractController
{
    /**
     * Edit a specific promotion
     * @Route("/promotion/{id}/edit", name="promo_edit")
     * @param Promotion     $promo
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response/JsonResponse
     */
    public function editPromo(Promotion $promo, Request $request, ObjectManager $manager)
    {
        $rep = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(EditPromotionType::class, $promo);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                $promo->clearModerator();
                if(isset($request->request->get('edit_promotion')['moderators'])){
                    $moderators = $request->request->get('edit_promotion')['moderators'];
                    for($i = 0 ; $i < sizeof($moderators) ; $i++){
                        $moderator = $this->getDoctrine()->getRepository(User::class)->find($moderators[$i]);
                        $promo->addModerator($moderator);
                    }
                }
                $manager->persist($promo);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'La promotion a bien été modifiée.'
                );
            }

            return $this->redirectToRoute('admin_all_promo');
        }


        $render = $this->render('admin/edit.html.twig', [
            'form'       => $form->createView(),
            'promo'      => $promo,
            'moderators' => $rep->findAllUserByRole()
        ]);

        $response = [
            "code"   => 200,
            "render" => $render->getContent()
        ];

        return new JsonResponse($response);
    }

    /**
     * Edit the current user
     * @Route("/user/{slug}/edit", name="user_edit")
     * @param User          $user
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editUserAccount(User $user, Request $request, ObjectManager $manager){
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR')){
            throw new AccessDeniedHttpException();
        }
        $imageName = "";

        $currentAvatar = $user->getAvatar();
        if(!empty($currentAvatar)){
            $imageName = $user->getAvatar();
        }
        $form = $this->createForm(AdminEditUserType::class, $user);

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

                $role_id = $request->request->get('edit_user')['userRoles'];
                if($role_id !== null){
                    $reprole = $this->getDoctrine()->getRepository(Role::Class);
                    $role = $reprole->find($role_id);
                    $role->addUser($user);
                    $user->setPromotion(null);
                }

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'L\'utilisateur a bien été mis à jour.'
                );
                return $this->redirectToRoute('admin_all_users', ['slug'=>'all']);
            }
        }

        return $this->render('admin/edit_user.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Edit a specific data
     * @Route("/donnee/{id}/edit", name="data_edit")
     * @param Data           $data
     * @param Request        $request
     * @param ObjectManager  $manager
     * @param UserRepository $rep
     * @return Response/JsonResponse
     */
    public function editData(Data $data, Request $request, ObjectManager $manager, UserRepository $rep)
    {
        $form = $this->createForm(DataType::class,$data);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            foreach($rep->findAll() as $user){
                $udata = new UserData();
                $udata->setData($data)
                      ->setUser($user);
                $manager->persist($udata);
            }
            $manager->persist($data);
            $manager->flush();

            $this->addFlash(
                'success',
                'La donnée a bien été modifiée.'
            );

            return $this->redirectToRoute('admin_all_datas');
        }

        $render = $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'data' => $data
        ]);

        $response = [
            "code"   => 200,
            "render" => $render->getContent()
        ];

        return new JsonResponse($response);
    }

    /**
     * Delete a specific data
     * @Route("/donnee/{id}/delete", name="data_delete")
     * @param Data $data
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteData(Data $data, ObjectManager $manager)
    {
        $manager->remove($data);
        $manager->flush();

        $this->addFlash(
            'success',
            'La donnée a bien été supprimée.'
        );

        return $this->redirectToRoute('admin_all_datas');
    }

    /**
     * Edit a specific help link
     * @Route("/aide/{id}/edit", name="help_edit")
     * @param Help          $help
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response/JsonResponse
     */
    public function editHelp(Help $help, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(HelpType::class, $help);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($help);
            $manager->flush();

            $this->addFlash(
                'success',
                'L\'aide a bien été modifiée.'
            );

            return $this->redirectToRoute('admin_all_helps');
        }

        $render = $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'help' => $help
        ]);

        $response = [
            "code"   => 200,
            "render" => $render->getContent()
        ];

        return new JsonResponse($response);
    }

    /**
     * Edit a specific skill
     * @Route("/competence/{id}/edit", name="skill_edit")
     * @param Skill         $skill
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response/JsonResponse
     */
    public function editSkill(Skills $skill, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($skill);
            $manager->flush();

            $this->addFlash(
                'success',
                'La compétence a bien été modifiée.'
            );

            return $this->redirectToRoute('admin_all_skills');
        }

        $render = $this->render('admin/edit.html.twig', [
            'form'  => $form->createView(),
            'skill' => $skill
        ]);

        $response = [
            "code"   => 200,
            "render" => $render->getContent()
        ];

        return new JsonResponse($response);
    }

    /**
     * Delete a skill
     * @Route("/competence/{id}/delete", name="skill_delete")
     * @param Skills        $skill
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteSkills(Skills $skill, ObjectManager $manager)
    {
        $manager->remove($skill);
        $manager->flush();

        return $this->redirectToRoute('admin_all_skills');
    }

    /**
     * Edit a specific project
     * @Route("/projet/{id}/edit", name="project_edit")
     * @param Project       $project
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response/JsonResponse
     */
    public function editProject(Project $project, Request $request, ObjectManager $manager)
    {
        $urep = $this->getDoctrine()->getRepository(User::class);
        $lrep = $this->getDoctrine()->getRepository(Language::class);
        $form = $this->createForm(EditProjectType::class, $project);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if(isset($request->request->get('edit_project')['users'])){
                $users_id = $request->request->get('edit_project')['users'];
                $moderatorCheck = false;
                $project->clearProject();
                for($i = 0 ; $i < sizeof($users_id) ; $i++){
                    $user = $urep->find($users_id[$i]);
                    $project->addUser($user);
                    if($user == $project->getModerator()){
                        $moderatorCheck = true;
                    }
                }

                if(!$moderatorCheck && !empty($project->getUsers())){
                    foreach($project->getUsers() as $user){
                        $project->setModerator($user);
                        break;
                    }
                }
            }

            if(isset($request->request->get('edit_project')['languages'])){
                $languages_id = $request->request->get('edit_project')['languages'];
                $project->clearLanguages();
                foreach($languages_id as $id){
                    $language = $lrep->find($id);
                    $project->addLanguage($language);
                }
            }

            if($project->getCompleted() && $project->getEndAt() == null){
                $project->setEndAt(new \DateTime());
            }
            if(!$project->getCompleted() && $project->getEndAt() != null){
                $project->setEndAt(null);
            }
            $manager->persist($project);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le projet a bien été modifié.'
            );
            return $this->redirectToRoute('admin_all_projects');
        }

        $render = $this->render('admin/edit.html.twig', [
            'form'      => $form->createView(),
            'project'   => $project,
            'users'     => $urep->findAllByCurrentPromo(),
            'languages' => $lrep->findAll()
        ]);

        $response = [
            "code"   => 200,
            "render" => $render->getContent()
        ];

        return new JsonResponse($response);
    }

    /**
     * Delete a specific language
     * @Route("/langage/{id}/delete", name="language_delete")
     * @param Language       $language
     * @param ObjectManager  $manager
     * @param HelpRepository $rep
     * @return Response
     */
    public function deleteLanguage(Language $language, ObjectManager $manager, HelpRepository $rep)
    {
        $helps = $rep->findBy(['language' => $language]);
        foreach($helps as $help){
            $help->setLanguage(null);
        }
        $manager->remove($language);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le langage a bien été supprimé.'
        );

        return $this->redirectToRoute('admin_all_datas');
    }

    /**
     * Remove a specific user's role
     * @Route("/user/{id}/{id_role}/remove", name="role_remove")
     * @Entity("role", expr="repository.find(id_role)")
     * @param User          $user
     * @param Role          $role
     * @param ObjectManager $manager
     * @return Response
     */
    public function removeRole(User $user, Role $role, ObjectManager $manager)
    {
        $role->removeUser($user);
        $manager->persist($role);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le role lié a '.$user->getFirstname().' bien été supprimé.'
        );

        return $this->redirectToRoute('admin_all_datas');
    }

    /**
     * Active a specific promotion and inactive all others promo
     * @Route("/promotions/{id}/active", name="promo_active")
     * @param Promotion           $promo
     * @param ObjectManager       $manager
     * @param PromotionRepository $rep
     * @return JsonResponse
     */
    public function activePromotion(Promotion $promo, ObjectManager $manager, PromotionRepository $rep)
    {
        foreach($rep->findAll() as $promo_others){
            $promo_others->setCurrent(false);
        }
        $promo->setCurrent(true);
        $manager->persist($promo);
        $manager->flush();

        $response = [
            "code" => 200
        ];
        return new JsonResponse($response);
    }

    /**
     * Complete a specific project
     * @Route("/project/{id}/completed", name="project_complete")
     * @param Project             $project
     * @param ObjectManager       $manager
     * @return JsonResponse
     */
    public function completeProject(Project $project, ObjectManager $manager)
    {
        if($project->getCompleted()){
            $project->setCompleted(false);
        } else {
            $project->setCompleted(true);
        }
        $manager->persist($project);
        $manager->flush();

        $response = [
            "code" => 200
        ];
        return new JsonResponse($response);
    }

    /**
     * Active/Inactive a specific user
     * @Route("/user/{slug}/active", name="user_active")
     * @param User          $user
     * @param ObjectManager $manager
     * @return JsonResponse
     */
    public function editActiveUser(User $user, ObjectManager $manager)
    {
        $user->getIsActive() ? $user->setIsActive(false) : $user->setIsActive(true);
        $manager->persist($user);
        $manager->flush();

        $response = [
            "code" => 200
        ];

        return new JsonResponse($response);
    }

    /**
     * Adding a specific project's correction
     * @Route("/projet/{id}/add-correction", name="project_edit_correction")
     * @param Project       $project
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response/JsonResponse
     */
    public function addCorrection(Project $project, Request $request, ObjectManager $manager)
    {
        $correction = new Correction();
        $form = $this->createForm(CorrectionType::class, $correction);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $project->addCorrection($correction);
            $manager->persist($project);
            $manager->flush();

            $this->addFlash(
                'success',
                'La correction a bien été ajoutée.'
            );

            return $this->redirectToRoute('admin_all_projects');
        }

        $render = $this->render('admin/edit.html.twig', [
            'form'              => $form->createView(),
            'projectCorrection' => $project
        ]);

        $response = [
            "code"   => 200,
            "render" => $render->getContent()
        ];

        return new JsonResponse($response);
    }

    /**
     * Add a specific project's task
     * @Route("/projet/{id}/add-task", name="project_add_task")
     * @param Project        $project
     * @param Request        $request
     * @param ObjectManager  $manager
     * @param UserRepository $rep
     * @return Response/JsonResponse
     */
    public function addTask(Project $project, Request $request, ObjectManager $manager, UserRepository $rep)
    {
        $task = new Task();
        $task->setProject($project);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $users = $request->request->get('task')['users'];
            foreach($users as $user_id){
                $user = $rep->find($user_id);
                $task->addUser($user);
            }
            $manager->persist($task);
            $manager->flush();

            // New notification
            $notif = $this->getUser()->createSenderNotif(Notification::TASK, $project->getSlug());
            $manager->persist($this->getUser());
            foreach($task->getUsers() as $user){
                if($user != $this->getUser()){
                    $user->createUserNotif($notif);
                    $manager->persist($user);
                }
            }
            $manager->flush();

            $this->addFlash(
                'success',
                'La tâche a bien été ajoutée.'
            );

            return $this->redirectToRoute('admin_all_projects');
        }

        $render = $this->render('admin/edit.html.twig', [
            'form'        => $form->createView(),
            'projectTask' => $project
        ]);

        $response = [
            "code"   => 200,
            "render" => $render->getContent()
        ];

        return new JsonResponse($response);
    }

    /**
     * Edit a specific correction
     * @Route("/correction/{id}/edit", name="correction_edit")
     * @param Correction    $correction
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response/JsonResponse
     */
    public function editCorrection(Correction $correction, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(CorrectionType::class, $correction);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($correction);
            $manager->flush();

            $this->addFlash(
                'success',
                'La correction a bien été modifiée.'
            );

            return $this->redirectToRoute('admin_all_corrections');
        }

        $render = $this->render('admin/edit.html.twig', [
            'form'        => $form->createView(),
            'correction'  => $correction
        ]);

        $response = [
            "code"   => 200,
            "render" => $render->getContent()
        ];

        return new JsonResponse($response);
    }

    /**
     * Delete a specific correction
     * @Route("/correction/{id}/delete", name="correction_delete")
     * @param Correction    $correction
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteCorrection(Correction $correction, ObjectManager $manager)
    {
        $manager->remove($correction);
        $manager->flush();

        $this->addFlash(
            'success',
            'La correction a bien été supprimée.'
        );

        return $this->redirectToRoute('admin_all_corrections');
    }
}
