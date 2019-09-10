<?php

namespace App\Controller;

use App\Entity\Files;
use App\Entity\UserFiles;
use App\Entity\UserNotif;
use App\Entity\Notification;
use App\Form\File\FilesType;
use App\Form\File\FilesAdminType;
use App\Repository\UserFilesRepository;
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
 * @Route("/fichiers", name="file_")
 */
class FileController extends AbstractController
{
    /**
     * File section
     *  - Show the files received
     *  - Show the files sent
     *  - Form to send a file to the mediators by the current user
     *  - Form to send a file to an user by the mediators
     * @Route("/index", name="user_show")
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

        $access = $user->hasRole();
        $file = new Files();

        $form = $access ? $this->createForm(FilesAdminType::class, $file) : $this->createForm(FilesType::class, $file);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $filename = $this->moveFile($form->get('name')->getData(),$form->get('title')->getData(), $rep);
            $file->setName($filename);

            // New notification
            $notif = $user->createSenderNotif(Notification::FILE);
            $manager->persist($user);

            if($access){
                $data = $request->request->get('files_admin');
                foreach($data['receiver'] as $receiver_id){
                    $receiver = $rep->find($receiver_id);
                    $receiver->createUserFile($this->getUser(), $file);
                    $receiver->createUserNotif($notif);
                    $manager->persist($receiver);
                }
            } else {
                $data = $request->request->get('files');
                foreach($data['receiver'] as $sup_user_id){
                    $sup_user = $rep->find($sup_user_id);
                    $sup_user->createUserFile($this->getUser(), $file);
                    $sup_user->createUserNotif($notif);
                    $manager->persist($sup_user);
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
     * Change status to a specific file (not "seen")
     * @Route("/{id}/changer_status", name="edit_status")
     * @param FileUser      $file
     * @param ObjectManager $manager
     * @return Response
     */
    public function editStatusFileUser(UserFiles $ufile, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() != $ufile->getSender()){
            throw new AccessDeniedException();
        }

        $ufile->setSeen(true);
        $manager->persist($ufile);
        $manager->flush();

        return new JsonResponse();
    }

    /**
     * @Route("/{id}/enlever", name="received_remove")
     *
     * @param Files $file
     * @param ObjectManager $manager
     * @param UserFilesRepository $rep
     * @return void
     */
    public function removeReceivedFile(Files $file, ObjectManager $manager, UserFilesRepository $rep)
    {
        $user = $this->getUser();
        $ufile = $rep->findOneBy([
            'receiver' => $user,
            'files'    => $file
        ]);
        $manager->remove($ufile);
        $manager->flush();

        return new JsonResponse();
    }

    /**
     * Delete all current user's files (sent)
     * @Route("/tout_supprimer", name="delete_all")
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteAllFilesUser(ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() == null){
            throw new AccessDeniedException();
        }

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
     * @Route("/{id}/supprimer", name="delete")
     * @param Files         $file
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteFileUser(Files $file, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() == null){
            throw new AccessDeniedException();
        }

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
                'status'  => 0,
                'message' => 'Download error'
            );
            $response = new JsonResponse ( $array, 400 );

            return $response;
        }
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

