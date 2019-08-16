<?php

namespace App\Controller;

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
use App\Form\Project\EditProjectType;
use App\Form\Promotion\PromotionType;
use App\Form\Project\AddCorrectionType;
use App\Repository\PromotionRepository;
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
        $form = $this->createForm(PromotionType::class, $promo);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($promo);
            $manager->flush();

            $this->addFlash(
                'success',
                'La promotion a bien été ajoutée.'
            );

            return $this->redirectToRoute('admin_all_promo');
        }

        $render = $this->render('admin/edit.html.twig', [
            'form' => $form->createView(),
            'promo' => $promo
        ]);

        $response = [
            "code"   => 200,
            "render" => $render->getContent()
        ];

        return new JsonResponse($response);
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
     * Edit a specific project
     * @Route("/projet/{id}/edit", name="project_edit")
     * @param Project       $project
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response/JsonResponse
     */
    public function editProject(Project $project, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(EditProjectType::class, $project);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($project);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le projet a bien été modifié.'
            );
            return $this->redirectToRoute('admin_all_projects');
        }

        $render = $this->render('admin/edit.html.twig', [
            'form'    => $form->createView(),
            'project' => $project
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
        $form = $this->createForm(AddCorrectionType::class, $correction);
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
}