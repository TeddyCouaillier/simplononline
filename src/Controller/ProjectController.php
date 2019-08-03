<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\Project\ProjectType;
use App\Repository\UserRepository;
use App\Form\Project\EditProjectType;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Task;
use App\Form\Project\TaskType;
use App\Entity\Language;

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
     * Show a specific project
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

        $process   = $project->getTaskByType(Task::PROCESSING);
        $todolist  = $project->getTaskByType(Task::TODOLIST);
        $completed = $project->getTaskByType(Task::COMPLETED);

        return $this->render('project/show.html.twig', [
            'project'   => $project,
            'form'      => $form->createView(),
            'process'   => $process,
            'todolist'  => $todolist,
            'completed' => $completed
        ]);
    }
}
