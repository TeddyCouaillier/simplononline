<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\Language;
use App\Form\Project\TaskType;
use App\Form\Project\ProjectType;
use App\Form\Project\EditTaskType;
use App\Repository\UserRepository;
use App\Form\Project\EditProjectType;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/project", name="project_")
 */
class ProjectController extends AbstractController
{
    /**
     * Show all projects
     * @Route("/all", name="all")
     * @param ProjectRepository $rep
     * @return Response
     */
    public function allProject(ProjectRepository $rep)
    {
        return $this->render('project/all.html.twig', [
            'projects' => $rep->findAll()
        ]);
    }

    /**
     * Create a project
     * @Route("/create", name="create")
     * @param Request        $request
     * @param ObjectManager  $manager
     * @param UserRepository $rep
     * @return Response
     */
    public function createProject(Request $request, ObjectManager $manager, UserRepository $rep)
    {
        $project = new Project();

        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $users_id = $request->request->get('project')['users'];
            $languages_id = $request->request->get('project')['languages'];
            foreach($users_id as $id){
                $user = $rep->find($id);
                $project->addUser($user);
            }
            foreach($languages_id as $id){
                $language = $this->getDoctrine()->getRepository(Language::class)->find($id);
                $project->addLanguage($language);
            }
            // Publisher
            $project->setModerator($this->getUser());
            $project->addUser($this->getUser());

            $manager->persist($project);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le projet a bien été créé.'
            );
            return $this->redirectToRoute('project_show', ['slug'=> $project->getSlug()]);
        }

        return $this->render('project/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Remove a project's specific user
     * @Route("/{slug}/remove", name="remove_user")
     * @param Project       $project
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function removeUser(Project $project, Request $request, ObjectManager $manager)
    {
        $id = $request->query->get('id');
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        foreach($project->getTasks() as $task){
            $user->removeTask($task);
        }

        $project->removeUser($user);
        $manager->persist($user);
        $manager->persist($project);
        $manager->flush();

        $this->addFlash(
            'success',
            'L\'utilisateur a bien été retiré.'
        );

        return $this->redirectToRoute('project_show', ['slug'=> $project->getSlug()]);
    }

    /**
     * Edit a specific project
     * @Route("/{slug}/edit", name="edit")
     * @param Project       $project
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
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
            return $this->redirectToRoute('project_show', ['slug'=> $project->getSlug()]);
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form'    => $form->createView()
        ]);
    }

    /**
     * Delete a specific project
     * @Route("/{slug}/delete", name="delete")
     * @param Project       $project
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteProject(Project $project, ObjectManager $manager)
    {
        $manager->remove($project);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le projet a bien été supprimé.'
        );

        return $this->redirectToRoute('project_all');
    }

    /**
     * Delete a specific task in a specific project
     * @Route("/{slug}/task/{id_task}/delete", name="delete_task")
     * @Entity("task", expr="repository.find(id_task)")
     * @param Project       $project
     * @param Task          $task
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteTaskProject(Project $project, Task $task, ObjectManager $manager)
    {
        $manager->remove($task);
        $manager->flush();

        $this->addFlash(
            'success',
            'La tache a bien été supprimée.'
        );

        return $this->redirectToRoute('project_show', ['slug'=> $project->getSlug()]);
    }

    /**
     * @Route("/{slug}/task/{id_task}/edit", name="edit_task")
     * @Entity("task", expr="repository.find(id_task)")
     * @param Porject       $project
     * @param Task          $task
     * @param ObjectManager $manager
     * @return Response/JsonResponse
     */
    public function editTaskProject(Project $project, Task $task, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(EditTaskType::class, $task);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $manager->persist($task);
            $manager->flush();

            $this->addFlash(
                'success',
                'La tâche a bien été ajoutée.'
            );

            return $this->redirectToRoute('project_show', ['slug' => $project->getSlug()]);
        }

        // Ajax calling
        $render = $this->render('project/edit_task.html.twig',[
            'form' => $form->createView(),
            'project' => $project,
            'task' => $task
        ]);
        $response = [
            "code" => 200,
            "render" => $render->getContent()
        ];
        return new JsonResponse($response);
    }

    /**
     * Ajax calling to see more task (by type)
     * @Route("/{slug}/seemore", name="seemore")
     * @param Project           $project
     * @param Request           $request
     * @param ProjectRepository $rep
     * @return JsonResponse
     */
    public function seeMoreTask(Project $project, Request $request, ProjectRepository $rep)
    {
        $offset = $request->request->get('offset');
        $type   = $request->request->get('type');
        $tasks  = $rep->findAllTasksByType($project, $type, $offset);

        $render = $this->render('project/_task_content.html.twig',[
            'project' => $project,
            'tasks' => $tasks
        ]);

        $response = [
            "code" => 200,
            "render" => $render->getContent(),
            "size" => sizeof($tasks)
        ];
        return new JsonResponse($response);
    }

    /**
     * Show a specific project and adding task form
     * @Route("/{slug}", name="show")
     * @param Project        $project
     * @param Request        $request
     * @param ObjectManager  $manager
     * @param UserRepository $rep
     * @return Response
     */
    public function showProject(Project $project, Request $request, ObjectManager $manager, UserRepository $rep)
    {
        $task = new Task();
        $task->setProject($project);

        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $users = $request->request->get('task')['users'];
            foreach($users as $user_id){
                $user = $rep->find($user_id);
                $task->addUser($user);
            }
            $manager->persist($task);
            $manager->flush();

            $this->addFlash(
                'success',
                'La tâche a bien été ajoutée.'
            );
        }

        // Repositories
        $reptask    = $this->getDoctrine()->getRepository(Task::class);
        $repproject = $this->getDoctrine()->getRepository(Project::class);

        // Get progressing datas
        $process_total  = $reptask->getTotalSubtaskByType($project, Task::PROCESSING);
        $todolist_total = $reptask->getTotalSubtaskByType($project, Task::TODOLIST);
        $process_done   = $reptask->getTotalSubtaskDoneByType($project, Task::PROCESSING);
        $todolist_done  = $reptask->getTotalSubtaskDoneByType($project, Task::TODOLIST);

        $process_progress  = $process_total != 0 ? ($process_done / $process_total * 100) : 0;
        $todolist_progress = $todolist_total != 0 ? ($todolist_done / $todolist_total * 100) : 0;

        // Get all task by type
        $process   = $repproject->findAllTasksByType($project, Task::PROCESSING);
        $todolist  = $repproject->findAllTasksByType($project, Task::TODOLIST);
        $completed = $repproject->findAllTasksByType($project, Task::COMPLETED);

        // Project datas
        $total = $reptask->getTotalSubtask($project);
        $done  = $reptask->getTotalSubtaskDone($project);
        $progress = $total != 0 ? ($done / $total * 100) : 0;

        return $this->render('project/show.html.twig', [
            'project'   => $project,
            'form'      => $form->createView(),
            'process'   => $process,
            'todolist'  => $todolist,
            'completed' => $completed,
            'progress'  => $progress,
            'process_progress'  => $process_progress,
            'todolist_progress' => $todolist_progress
        ]);
    }
}
