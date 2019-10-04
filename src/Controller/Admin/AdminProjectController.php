<?php

namespace App\Controller\Admin;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\Language;
use App\Entity\Notification;
use App\Form\Project\TaskType;
use App\Repository\UserRepository;
use App\Form\Project\EditProjectType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin_")
 */
class AdminProjectController extends AbstractController
{
    /**
     * Edit a specific project
     * @Route("/projet/{id}/modifier", name="project_edit")
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
            return $this->redirectToRoute('project_all');
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
     * Complete a specific project
     * @Route("/project/{id}/terminer", name="project_complete")
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

        return new JsonResponse();
    }

    /**
     * Add a specific project's task
     * @Route("/projet/{id}/ajouter-tache", name="project_add_task")
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

            return $this->redirectToRoute('project_all');
        }

        $render = $this->render('admin/edit.html.twig', [
            'form'        => $form->createView(),
            'projectTask' => $project
        ]);

        $response = [ "render" => $render->getContent() ];

        return new JsonResponse($response);
    }
}