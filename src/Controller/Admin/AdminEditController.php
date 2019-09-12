<?php

namespace App\Controller\Admin;

use App\Entity\Data;
use App\Entity\Help;
use App\Entity\Skills;
use App\Entity\Project;
use App\Entity\Language;
use App\Entity\UserData;
use App\Entity\Correction;
use App\Entity\UserDeadline;
use App\Form\Data\DataType;
use App\Form\Help\HelpType;
use App\Form\Skill\SkillType;
use App\Repository\HelpRepository;
use App\Repository\UserRepository;
use App\Form\Project\CorrectionType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin_")
 */
class AdminEditController extends AbstractController
{
    /**
     * Edit a specific data
     * @Route("/donnee/{id}/modifier", name="data_edit")
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
     * @Route("/donnee/{id}/supprimer", name="data_delete")
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
     * @Route("/aide/{id}/modifier", name="help_edit")
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
     * @Route("/competence/{id}/modifier", name="skill_edit")
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
     * @Route("/competence/{id}/supprimer", name="skill_delete")
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
     * Delete a specific language
     * @Route("/langage/{id}/supprimer", name="language_delete")
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
     * Adding a specific project's correction
     * @Route("/projet/{id}/ajouter-correction", name="project_edit_correction")
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

        $response = [ "render" => $render->getContent() ];

        return new JsonResponse($response);
    }

    /**
     * Edit a specific correction
     * @Route("/correction/{id}/modifier", name="correction_edit")
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
     * @Route("/correction/{id}/supprimer", name="correction_delete")
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

    /**
     * Valide/Invalide a specific deadline
     * @route("/deadlines/{id}/edit-state", name="deadlines_edit_state")
     * @param UserDeadline  $udeadline
     * @param Request       $request
     * @param ObjectManager $manager
     * @return JsonResponse
     */
    public function editDeadlineState(UserDeadline $udeadline, Request $request, ObjectManager $manager)
    {
        $validate = (bool)$request->request->get('validate');
        $udeadline->setValidate(!$validate);

        $manager->persist($udeadline);
        $manager->flush();

        return new JsonResponse();
    }

    /**
     * Delete a specific deadline
     * @Route("/deadlines/{id}/delete", name="deadlines_delete")
     * @param UserDeadline  $udeadline
     * @param ObjectManager $manager
     * @return JsonResponse
     */
    public function deleteDeadline(UserDeadline $udeadline, ObjectManager $manager)
    {
        $manager->remove($udeadline);
        $manager->flush();

        return new JsonResponse();
    }
}
