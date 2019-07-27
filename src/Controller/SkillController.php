<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Skills;
use App\Entity\UserSkills;
use App\Form\Skill\SkillType;
use App\Repository\SkillsRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/competences", name="skill_")
 */
class SkillController extends AbstractController
{
    /**
     * Show all skills + adding form
     * @Route("", name="show")
     * @param UserSkills $uskill
     * @return Response
     */
    public function showSkills(SkillsRepository $rep, Request $request, ObjectManager $manager)
    {
        $skill = new Skills();
        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $users = $this->getDoctrine()->getRepository(User::class)->findAll();
            $skill->initializeUsers($users);

            $manager->persist($skill);
            $manager->flush();
        }
        return $this->render('skill/show.html.twig',[
            'skills' => $rep->findAll(),
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete a skill
     * @Route("/{id}/delete", name="delete")
     * @IsGranted("ROLE_ADMIN")
     * @param Skills        $skill
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteSkills(Skills $skill, ObjectManager $manager)
    {
        $manager->remove($skill);
        $manager->flush();
        return $this->redirectToRoute('skill_show');
    }
}
