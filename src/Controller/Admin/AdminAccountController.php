<?php

namespace App\Controller\Admin;

use App\Entity\Help;
use App\Entity\User;
use App\Entity\Files;
use App\Entity\Project;
use App\Entity\Schedule;
use App\Entity\Promotion;
use App\Entity\UserNotif;
use App\Entity\Correction;
use App\Entity\Notification;
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
        $srep = $this->getDoctrine()->getRepository(Schedule::class);

        $form = $this->createForm(FilesAdminType::class, $file);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $filename = $this->moveFile($form->get('name')->getData(),$form->get('title')->getData(), $urep);
            $file->setName($filename);

            // New notification
            $notif = $user->createSenderNotif(Notification::FILE);
            $manager->persist($user);

            $data = $request->request->get('files_admin');
            foreach($data['receiver'] as $receiver_id){
                $receiver = $urep->find($receiver_id);
                $receiver->createUserFile($user, $file);
                $receiver->createUserNotif($notif);
                $manager->persist($receiver);
            }

            $manager->flush();

            $this->addFlash(
                'success',
                'Le fichier a bien été envoyé.'
            );
        }

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
            'promo'       => $promo,
            'schedules'   => $srep->findAllNow(),
            'schedulesT'  => $srep->findAllFutures()
        ]);
    }

    /**
     * Move the file upload to the file directory
     * @param string         $name  the upload file name
     * @param string         $title the title given by the user
     * @param UserRepository $rep
     * @return string
     */
    public function moveFile($name, $title, $rep)
    {
        $file = $name;
        $fileName = "";
        if($file != NULL)
        {
            $lastUserFile = $rep->findOneBy([], ['id' => 'desc']);
            $id = $lastUserFile !== null ? $lastUserFile->getId()+1 : 1;
            $fileName = str_replace(" ","-",$title);
            $fileName .= $id.'.'.$file->guessExtension();

            $file->move(
                $this->getParameter('file_directory'),
                $fileName
            );
        }
        return $fileName;
    }
}