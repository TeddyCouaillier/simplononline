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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProjectController extends AbstractController
{
    /**
     * @rOUTE("/project/all", name="project_all")
     */
    public function allProject(ProjectRepository $rep)
    {
        return $this->render('project/all.html.twig', [
            'projects' => $rep->findAll()
        ]);
    }

    /**
     * @Route("/project/create", name="project_create")
     */
    public function createProject(Request $request, ObjectManager $manager, UserRepository $rep)
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $users_id = $request->request->get('project')['users'];
            foreach($users_id as $id){
                $user = $rep->find($id);
                if($user == null){ die;}
                $project->addUser($user);
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
     * @Route("/project/{slug}/edit", name="project_edit")
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
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/project/{slug}", name="project_show")
     */
    public function showProject(Project $project)
    {

        // Check last connected date and now
        $today = new \DateTime();
        $created = $project->getCreatedAt();
        $interval = date_diff($created, $today);
        $day = intval($interval->format('%R%a'));

        switch(true){
            case ($day == 0):
                $time = 'Aujourd\'hui';
                break;
            case ($day == 1):
                $time = 'Hier';
                break;
            case ($day > 1 && $day < 7):
                $time = 'Il y a'.$day.'jours';
                break;
            case ($day >= 7 && $day <= 14):
                $time = 'Il y a une semaine';
                break;
            case ($day >= 15 && $day <= 21):
                $time = 'Il y a deux semaines';
                break;
            case ($day >= 22 && $day <= 29):
                $time = 'Il y a trois semaines';
                break;
            case ($day >= 30 && $day <= 365):
                $month = intval($day / 30);
                $time = 'Il y a '.$month.' mois';
                break;
            default:
                $time = 'Il y a trop longtemps';
        }
        // if($interval->format('%R%a') > 0){
        //     $updated = false;

        return $this->render('project/show.html.twig', [
            'project' => $project,
            'time' => $time
        ]);
    }
}
