<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Files;
use App\Entity\UserFiles;
use App\Entity\UserNotif;
use App\Entity\Notification;
use App\Form\File\FilesType;
use App\Form\File\FilesAdminType;
use App\Repository\UserRepository;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("user/fichiers", name="file_")
 */
class FileController extends AbstractController
{
    /**
     * File section
     *  - Show the files received
     *  - Show the files sent
     *  - Form to send a file to the mediators by the current user
     *  - Form to send a file to an user by the mediators
     * @Route("", name="user_show")
     * @param Request        $request
     * @param ObjectManager  $manager
     * @param UserRepository $rep
     * @return Response
     */
    public function showFileUser(Request $request, ObjectManager $manager, UserRepository $rep)
    {
        if($request->query->get('seen') != null){
            $unotif = $this->getDoctrine()->getRepository(UserNotif::class)->find($request->query->get('seen'));
            if($unotif != null){
                $unotif->setSeen(true);
                $manager->persist($unotif);
                $manager->flush();
            }
        }

        $user = $this->getUser();

        $access = $this->container->get('security.authorization_checker')->isGranted(User::MEDIATEUR);
        $file = new Files();

        $form = $access ? $this->createForm(FilesAdminType::class, $file) : $this->createForm(FilesType::class, $file);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $filename = $this->moveFile($form->get('name')->getData(),$form->get('title')->getData());
            $file->setName($filename);

            // New notification
            $notif = $user->createSenderNotif(Notification::FILE);
            $manager->persist($user);

            if($access){
                $data = $request->request->get('files_admin');
                foreach($data['receiver'] as $receiver_id){
                    $receiver = $rep->find($receiver_id);
                    $important = isset($data['important']) ? true : false;
                    $receiver->createUserFile($this->getUser(), $file, $important);
                    $receiver->createUserNotif($notif);
                    $manager->persist($receiver);
                }
            } else {
                $data = $request->request->get('files');
                foreach($rep->findAllByUserRole(User::MEDIATEUR) as $mediateur){
                    $important = isset($data['important']) ? true : false;
                    $mediateur->createUserFile($this->getUser(), $file, $important);
                    $mediateur->createUserNotif($notif);
                    $manager->persist($mediateur);
                }
            }
            $manager->flush();

            $this->addFlash(
                'success',
                'Le fichier a bien été envoyé.'
            );
        }

        $sending = $this->getDoctrine()->getRepository(UserFiles::class)->findBy([
            'sender' => $user
        ]);

        return $this->render('file/show_files.html.twig', [
            'user'    => $user,
            'sending' => $sending,
            'form'    => $form->createView(),
            'access'  => $access
        ]);
    }

    /**
     * Change status to a specific file (not "important")
     * @Route("/{id}/editStatus", name="edit_status")
     * @param FileUser      $file
     * @param ObjectManager $manager
     * @return Response
     */
    public function editStatusFileUser(UserFiles $ufile, ObjectManager $manager)
    {
        $ufile->setImportant(false);
        $manager->persist($ufile);
        $manager->flush();
        return $this->redirectToRoute('file_user_show');
    }

    /**
     * Delete all current user's files (sent)
     * @Route("/delete_all", name="delete_all")
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteAllFilesUser(ObjectManager $manager)
    {
        $user = $this->getUser();
        foreach($user->getSenderFiles() as $ufile){
            $file = $ufile->getFiles();
            if($file != null){
                $this->removeFile($file->getName());
            }
            $manager->remove($ufile);
        }
        $manager->flush();

        $this->addFlash(
            'success',
            'Les fichiers ont bien été supprimés.'
        );

        return $this->redirectToRoute('file_user_show');
    }

    /**
     * Delete a file by a sender only
     * @Route("/{id}/delete", name="delete")
     * @param Files         $file
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteFileUser(Files $file, ObjectManager $manager)
    {
        $rep = $this->getDoctrine()->getRepository(UserFiles::class);
        $this->removeFile($file->getName());

        $ufiles = $rep->findBy([
            'sender' => $this->getUser(),
            'files' => $file
        ]);
        foreach($ufiles as $ufile){
            $manager->remove($ufile);
        }
        $manager->remove($file);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le fichier a bien été supprimé.'
        );

        return $this->redirectToRoute('file_user_show');
    }

    /**
     * Download a specific file
     * @Route("/{id}", name="download")
     * @param Files $file
     * @return JsonResponse
     */
    public function downloadFile(Files $file)
    {
        try {
            if (!$file) {
                $array = array (
                    'status' => 0,
                    'message' => 'File does not exist'
                );
                $response = new JsonResponse ( $array, 200 );

                return $response;
            }
            $file_with_path = $this->getParameter('file_directory') . "/" . $file->getName();
            $response = new BinaryFileResponse ( $file_with_path );
            $response->headers->set ( 'Content-Type', 'text/plain' );
            $response->setContentDisposition ( ResponseHeaderBag::DISPOSITION_ATTACHMENT, $file->getTitle() );

            return $response;
        } catch ( Exception $e ) {
            $array = array (
                'status' => 0,
                'message' => 'Download error'
            );
            $response = new JsonResponse ( $array, 400 );

            return $response;
        }
    }

    /**
     * Move the file upload to the file directory
     * @param string $name  the upload file name
     * @param string $title the title given by the user
     * @return string
     */
    public function moveFile($name, $title)
    {
        $file = $name;
        $fileName = "";
        if($file != NULL)
        {
            $lastUserFile = $this->getDoctrine()->getRepository(UserFiles::class)->findOneBy([], ['id' => 'desc']);
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

    /**
     * Remove the file from the directory
     * @param string $filename the file name
     * @return void
     */
    public function removeFile($filename)
    {
        $file = $this->getParameter('file_directory') . "/" . $filename;
        $filesystem = new Filesystem();
        $filesystem->remove($file);
    }
}

