<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Skills;
use App\Entity\UserSkills;
use App\Form\Skill\SkillType;
use App\Repository\UserSkillsRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/competences", name="skill_")
 */
class SkillController extends AbstractController
{
    /**
     * Show all skills
     * @Route("", name="show")
     * @param UserSkills $uskill
     * @return Response
     */
    public function showSkills(UserSkillsRepository $rep)
    {
        return $this->render('skill/show.html.twig',[
            // 'form' => $form->createView()
        ]);
    }

    /**
     * Delete a skill
     * @Route("/{id}/delete", name="delete")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
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
