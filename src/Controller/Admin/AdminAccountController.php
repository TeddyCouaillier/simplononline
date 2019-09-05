<?php

namespace App\Controller\Admin;

use App\Entity\Help;
use App\Entity\User;
use App\Entity\Files;
use App\Entity\Project;
use App\Entity\Promotion;
use App\Entity\UserNotif;
use App\Entity\Correction;
use App\Form\File\FilesAdminType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
            'projects'    => $promo == null ? null : $prep->findAllProjectByPromo($promo),
            'weather'     => $promo == null ? -1 : round($urep->findWeatherAvgByPromo($promo)),
            'completed'   => $promo == null ? 0 : count($prep->findBy(['completed' => true])),
            'helps'       => $promo == null ? 0 : count($hrep->findAllHelpByPromo($promo)),
            'users'       => $promo == null ? 0 : count($urep->findBy(['promotion' => $promo])),
            'corrections' => $promo == null ? 0 : count($crep->findAllCorrectionByPromo($promo)),
            'promo'       => $promo

        ]);
    }
}