<?php

namespace App\Controller\Admin;

use App\Entity\Data;
use App\Entity\User;
use App\Entity\Skills;
use App\Entity\Language;
use App\Entity\UserData;
use App\Entity\Correction;
use App\Entity\UserSkills;
use App\Form\Data\DataType;
use App\Service\Pagination;
use App\Form\Skill\SkillType;
use App\Repository\UserRepository;
use App\Repository\SkillsRepository;
use App\Form\Project\AddCorrectionType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/admin", name="admin_")
 */
class AdminShowController extends AbstractController
{
    /**
     * Show all skills + adding skill form
     * @Route("/competences", name="all_skills")
     * @param Request          $request
     * @param ObjectManager    $manager
     * @param SkillsRepository $rep
     * @return Response
     */
    public function allSkills(Request $request, ObjectManager $manager, SkillsRepository $rep)
    {
        $skill = new Skills();
        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($skill);
            $users = $this->getDoctrine()->getRepository(User::class)->findAll();
            foreach($users as $user){
                $uskill = new UserSkills();
                $uskill->setSkill($skill)
                       ->setUser($user)
                       ->setLevel(0);
                $manager->persist($uskill);
            }

            $manager->flush();

            $this->addFlash(
                'success',
                'La promotion a bien été ajoutée.'
            );
        }

        return $this->render('admin/all_skills.html.twig',[
            'skills' => $rep->findAll(),
            'form'   => $form->createView()
        ]);
    }

    /**
     * Show all trainings courses
     * @Route("/stages", name="all_trainings")
     * @param UserRepository $rep
     * @return Response
     */
    public function allTrainings(UserRepository $rep)
    {
        return $this->render('admin/all_trainings.html.twig', [
            'users' => $rep->findAllByCurrentPromo()
        ]);
    }

    /**
     * Show all datas + adding data form + adding language form
     * @Route("/donnees", name="all_datas")
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function allDatas(Request $request, ObjectManager $manager)
    {
        $drep = $this->getDoctrine()->getRepository(Data::class);
        $lrep = $this->getDoctrine()->getRepository(Language::class);
        $urep = $this->getDoctrine()->getRepository(User::class);

        $language = new Language();
        $data     = new Data();
        $formLanguage =
            $this->createFormBuilder($language)
                 ->add('label',null,[ 'attr' => ['placeholder' => 'HTML, PHP, Symfony...']])
                 ->getForm();
        $formData = $this->createForm(DataType::class, $data);

        $formLanguage->handleRequest($request);
        $formData->handleRequest($request);
        if($formLanguage->isSubmitted() && $formLanguage->isValid()){
            $manager->persist($language);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le langage a bien été ajouté.'
            );
        }
        else if($formData->isSubmitted() && $formData->isValid()){
            $manager->persist($data);
            foreach($urep->findAll() as $user){
                $udata = new UserData();
                $udata->setData($data)
                      ->setUser($user);
                $manager->persist($udata);
            }
            $manager->flush();

            $this->addFlash(
                'success',
                'La donnée a bien été ajoutée.'
            );
        }

        return $this->render('admin/all_datas.html.twig', [
            'datas'     => $drep->findAll(),
            'languages' => $lrep->findAll(),
            'roles'     => $urep->findAllUserByRole(),
            'formLanguage' => $formLanguage->createView(),
            'formData'     => $formData->createView()
        ]);
    }

    /**
     * Show all corrections
     * @Route("/corrections/{page<\d+>?1}", name="all_corrections")
     * @param integer       $page
     * @param Pagination    $pagination
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function allCorrections(int $page, Pagination $pagination, Request $request, ObjectManager $manager)
    {
        $pagination->setEntity(Correction::class)
                   ->setPage($page);

        $correction = new Correction();
        $form = $this->createForm(AddCorrectionType::class, $correction);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($correction);
            $manager->flush();

            $this->addFlash(
                'success',
                'La correction a bien été ajoutée.'
            );
        }
        return $this->render('admin/all_corrections.html.twig', [
            'form'        => $form->createView(),
            'pagination' => $pagination
        ]);
    }
}