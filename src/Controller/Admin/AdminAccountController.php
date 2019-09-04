<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\Data;
use App\Entity\Help;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Files;
use App\Entity\Skills;
use App\Entity\Project;
use App\Entity\Language;
use App\Entity\UserData;
use App\Entity\Promotion;
use App\Entity\UserNotif;
use App\Entity\Correction;
use App\Entity\UserSkills;
use App\Form\Data\DataType;
use App\Form\Help\HelpType;
use App\Service\Pagination;
use App\Form\Skill\SkillType;
use App\Form\File\FilesAdminType;
use App\Form\User\CreateUserType;
use App\Repository\HelpRepository;
use App\Repository\UserRepository;
use App\Repository\SkillsRepository;
use App\Form\Promotion\PromotionType;
use App\Repository\ProjectRepository;
use App\Form\Project\AddCorrectionType;
use App\Repository\PromotionRepository;
use App\Repository\CorrectionRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin", name="admin_")
 */
class AdminAccountController extends AbstractController
{
    /**
     * @Route("/account", name="account")
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function showAccount(Request $request, ObjectManager $manager)
    {
        $file = new Files();
        $user = $this->getUser();
        $prep = $this->getDoctrine()->getRepository(Project::class);
        $hrep = $this->getDoctrine()->getRepository(Help::class);
        $urep = $this->getDoctrine()->getRepository(User::class);
        $pjrp = $this->getDoctrine()->getRepository(Promotion::class);
        $crep = $this->getDoctrine()->getRepository(Correction::class);

        $form = $this->createForm(FilesAdminType::class, $file);

        if($request->query->get('seen') != null){
            $unotif = $this->getDoctrine()->getRepository(UserNotif::class)->find($request->query->get('seen'));
            if($unotif != null){
                $unotif->setSeen(true);
                $manager->persist($unotif);
                $manager->flush();
            }
        }

        $promo = $pjrp->findOneBy(['current' => true]);

        return $this->render('user/account.html.twig', [
            'user' => $user,
            'date' => new \DateTime(),
            'form' => $form->createView(),
            'projects'    => $prep->findAllProjectByPromo($promo),
            'completed'   => count($prep->findBy(['completed' => true])),
            'helps'       => count($hrep->findAllHelpByPromo($promo)),
            'users'       => count($urep->findBy(['promotion' => $promo])),
            'corrections' => count($crep->findAllCorrectionByPromo($promo)),
            'weather'     => round($urep->findWeatherAvgByPromo($promo)),
            'promo'       => $promo

        ]);
    }




}