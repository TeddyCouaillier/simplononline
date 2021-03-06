<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Entity\Project;
use App\Entity\Language;
use App\Entity\UserNotif;
use App\Entity\Correction;
use App\Service\Pagination;
use App\Entity\Notification;
use App\Form\Project\TaskType;
use App\Form\Project\ProjectType;
use App\Form\Project\EditTaskType;
use App\Repository\UserRepository;
use App\Form\Project\CorrectionType;
use App\Form\Project\EditProjectType;
use App\Repository\ProjectRepository;
use App\Repository\LanguageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/projet", name="project_")
 */
class ProjectController extends AbstractController
{
    /**
     * Show all projects
     * @Route("s/{page<\d+>?1}", name="all")
     * @param integer    $page       current page
     * @param Pagination $pagination pagination service
     * @return Response
     */
    public function allProject(int $page, Pagination $pagination, LanguageRepository $lrep)
    {
        $pagination->setEntity(Project::class)
                   ->setPage($page)
                   ->setLimit(20);

        return $this->render('project/all.html.twig', [
            'pagination' => $pagination,
            'languages'  => $lrep->findAll()
        ]);
    }

    /**
     * Show all projects by language
     * @Route("s/{slug}/{page<\d+>?1}", name="all_by_language")
     * @param integer            $page
     * @param Language           $language
     * @param ProjectRepository  $prep
     * @param LanguageRepository $lrep
     * @return Response
     */
    public function allProjectByLanguage(int $page, Language $language, ProjectRepository $prep, LanguageRepository $lrep)
    {
        $limit = 20;
        $offset = $page * $limit - $limit;
        $projects = $prep->findAllByLanguageLimit($language, $limit, $offset);
        $total = count($prep->findAllByLanguage($language));
        $pages = ceil($total / $limit);

        return $this->render('project/all.html.twig', [
            'page'       => $page,
            'pages'      => $pages,
            'projects'   => $projects,
            'languages'  => $lrep->findAll(),
            'language'   => $language
        ]);
    }

    /**
     * Create a project
     * @Route("/nouveau", name="create")
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
            if(isset($request->request->get('project')['users'])){
                $users_id     = $request->request->get('project')['users'];
                foreach($users_id as $id){
                    $user = $rep->find($id);
                    $project->addUser($user);
                }
            }
            if(isset($request->request->get('project')['languages'])){
                $languages_id = $request->request->get('project')['languages'];
                foreach($languages_id as $id){
                    $language = $this->getDoctrine()->getRepository(Language::class)->find($id);
                    $project->addLanguage($language);
                }
            }

            // Publisher
            $project->setModerator($this->getUser());
            $project->addUser($this->getUser());

            $manager->persist($project);
            $manager->flush();

            // New notification
            $notif = $this->getUser()->createSenderNotif(Notification::PROJECT, $project->getSlug());
            $manager->persist($this->getUser());
            foreach($project->getUsers() as $user){
                if($user != $this->getUser()){
                    $user->createUserNotif($notif);
                    $manager->persist($user);
                }
            }
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
     * @Route("/{slug}/retirer", name="remove_user")
     * @param Project       $project
     * @param User          $user
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function removeUser(Project $project, Request $request, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && $project->getModerator() != $this->getUser() ) {
            throw new AccessDeniedException();
        }

        $id = $request->query->get('id');
        $user = $this->getDoctrine()->getRepository(User::class)->find($id);

        if($project->getModerator() == $user){
            $i = 0;
            foreach($project->getUsers() as $member){
                if($i++ == 1){
                    $project->setModerator($member);
                    break;
                }
            }
            if($project->getModerator() == $user){
                $this->addFlash(
                    'warning',
                    'Suppression impossible (dernier membre).'
                );
                return $this->redirectToRoute('project_show', ['slug'=> $project->getSlug()]);
            }
        }

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
     * @Route("/{slug}/modifier", name="edit")
     * @param Project       $project
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editProject(Project $project, Request $request, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && $project->getModerator() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        $lrep = $this->getDoctrine()->getRepository(Language::class);
        $urep = $this->getDoctrine()->getRepository(User::class);

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

            $manager->persist($project);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le projet a bien été modifié.'
            );
            return $this->redirectToRoute('project_show', ['slug'=> $project->getSlug()]);
        }

        return $this->render('project/edit.html.twig', [
            'project'   => $project,
            'users'     => $urep->findAllByCurrentPromo(),
            'languages' => $lrep->findAll(),
            'form'      => $form->createView()
        ]);
    }

    /**
     * Delete a specific project
     * @Route("/{slug}/supprimer", name="delete")
     * @param Project       $project
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteProject(Project $project, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && $project->getModerator() != $this->getUser()) {
            throw new AccessDeniedException();
        }

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
     * @Route("/{slug}/tache/{id_task}/supprimer", name="delete_task")
     * @Entity("task", expr="repository.find(id_task)")
     * @param Project       $project
     * @param Task          $task
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteTaskProject(Project $project, Task $task, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') &&  !$project->checkUserProject($this->getUser())){
            throw new AccessDeniedException();
        }

        $manager->remove($task);
        $manager->flush();

        $this->addFlash(
            'success',
            'La tache a bien été supprimée.'
        );

        return $this->redirectToRoute('project_show', ['slug'=> $project->getSlug()]);
    }

    /**
     * Ajax calling for task edit
     * @Route("/{slug}/tache/{id_task}/modifier", name="edit_task")
     * @Entity("task", expr="repository.find(id_task)")
     * @param Project       $project
     * @param Task          $task
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response/JsonResponse
     */
    public function editTaskProject(Project $project, Task $task, Request $request, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$project->checkUserProject($this->getUser())){
            throw new AccessDeniedException();
        }

        $rep = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(EditTaskType::class, $task);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $task->clearTask();
            if(isset($request->request->get('edit_task')['users'])){
                $users_id = $request->request->get('edit_task')['users'];

                for($i = 0 ; $i < sizeof($users_id) ; $i++){
                    $user = $rep->find($users_id[$i]);
                    $task->addUser($user);
                }
            }

            $task->checkType();
            $manager->persist($task);
            $manager->flush();

            $this->addFlash(
                'success',
                'La tâche a bien été ajoutée.'
            );

            return $this->redirectToRoute('project_show', ['slug' => $project->getSlug()]);
        }
        if($form->isSubmitted() && !$form->isValid())
        {
            $this->addFlash(
                'warning',
                'Un problème est survenu, vérifiez votre saisie.'
            );

            return $this->redirectToRoute('project_show', ['slug' => $project->getSlug()]);
        }

        $users = $rep->findAllByTask($task);
        /** @var $contributors all task contributors (within/without the project) */
        $contributors = new ArrayCollection($users);
        foreach($project->getUsers() as $user){
            if (!$contributors->contains($user)) {
                $contributors[] = $user;
            }
        }

        // Ajax calling
        $render = $this->render('project/edit_task.html.twig',[
            'form'    => $form->createView(),
            'project' => $project,
            'task'    => $task,
            'contributors' => $contributors
        ]);
        $response = [
            "code"   => 200,
            "render" => $render->getContent(),
        ];
        return new JsonResponse($response);
    }

    /**
     * Ajax calling to see more task (by type)
     * @Route("/{slug}/voirplus", name="seemore")
     * @param Project           $project
     * @param Request           $request
     * @param ProjectRepository $rep
     * @return Response/JsonResponse
     */
    public function seeMoreTask(Project $project, Request $request, ProjectRepository $rep)
    {
        if(!$request->isXmlHttpRequest()) {
            return $this->redirectToRoute('project_show', ['slug' => $project->getSlug()]);
        }

        $offset = $request->request->get('offset');
        $type   = $request->request->get('type');
        $tasks  = $rep->findAllTasksByTypeLimit($project, $type, $offset);

        $render = $this->render('project/_task_content.html.twig',[
            'project' => $project,
            'tasks'   => $tasks
        ]);

        $response = [
            "code"   => 200,
            "render" => $render->getContent(),
            "size"   => sizeof($tasks)
        ];
        return new JsonResponse($response);
    }

    /**
     * @Route("/{id_project}/{id}/supprimer", name="remove_language")
     * @Entity("project", expr="repository.find(id_project)")
     * @Entity("language", expr="repository.find(id)")
     * @param Project $project
     * @param Language $language
     * @param ObjectManager $manager
     * @return JsonResponse
     */
    public function removeLanguage(Project $project, Language $language, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && $project->getModerator() != $this->getUser()) {
            throw new AccessDeniedException();
        }

        $project->removeLanguage($language);
        $manager->persist($project);
        $manager->flush();

        return new JsonResponse();
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
        if($request->query->get('seen') != null){
            $unotif = $this->getDoctrine()->getRepository(UserNotif::class)->find($request->query->get('seen'));
            if($unotif != null){
                $unotif->setSeen(true);
                $manager->persist($unotif);
                $manager->flush();
            }
        }

        $correction = new Correction();
        $task = new Task();
        $task->setProject($project);

        $formCorrect = $this->createForm(CorrectionType::class, $correction);
        $form = $this->createForm(TaskType::class, $task);

        $formCorrect->handleRequest($request);
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
        }

        if($formCorrect->isSubmitted() && $formCorrect->isValid()){
            $project->addCorrection($correction);
            $manager->persist($project);
            $manager->flush();

            $this->addFlash(
                'success',
                'La correction a bien été ajoutée.'
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

        $process_progress  = $process_total  != 0 ? ($process_done  / $process_total  * 100) : 0;
        $todolist_progress = $todolist_total != 0 ? ($todolist_done / $todolist_total * 100) : 0;

        // Get all task by type
        $process   = $repproject->findAllTasksByTypeLimit($project, Task::PROCESSING);
        $todolist  = $repproject->findAllTasksByTypeLimit($project, Task::TODOLIST);
        $completed = $repproject->findAllTasksByTypeLimit($project, Task::COMPLETED);

        // Get all task by type total
        $processTotal   = count($repproject->findAllTasksByType($project, Task::PROCESSING));
        $todolistTotal  = count($repproject->findAllTasksByType($project, Task::TODOLIST));
        $completedTotal = count($repproject->findAllTasksByType($project, Task::COMPLETED));

        // Project datas
        $total = $reptask->getTotalSubtask($project);
        $done  = $reptask->getTotalSubtaskDone($project);
        $progress = $total != 0 ? ($done / $total * 100) : 0;

        return $this->render('project/show.html.twig', [
            'project'           => $project,
            'process'           => $process,
            'processTotal'      => $processTotal,
            'todolist'          => $todolist,
            'todolistTotal'     => $todolistTotal,
            'completed'         => $completed,
            'completedTotal'    => $completedTotal,
            'progress'          => $progress,
            'form'              => $form->createView(),
            'formCorrect'       => $formCorrect->createView(),
            'process_progress'  => $process_progress,
            'todolist_progress' => $todolist_progress
        ]);
    }
}
