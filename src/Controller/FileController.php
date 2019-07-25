<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Files;
use App\Entity\UserFiles;
use App\Form\File\UserFilesType;
use App\Repository\UserRepository;
use App\Form\File\UserFilesAdminType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class FileController extends AbstractController
{

    /**
     * File section
     *  - Show the files received
     *  - Show the files sent
     *  - Form to send a file to the mediators by the current user
     *  - Form to send a file to an user by the mediators
     * @Route("user/fichiers", name="user_show_file")
     * @param Request        $request
     * @param ObjectManager  $manager
     * @param UserRepository $rep
     * @return Response
     */
    public function showFileUser(Request $request, ObjectManager $manager, UserRepository $rep)
    {
        $user = $this->getUser();
        $sending = $this->getDoctrine()->getRepository(UserFiles::class)->findBy([
            'sender' => $user
        ]);
        $access = $user->hasRole(User::MEDIATEUR);
        $ufile = new UserFiles();

        if($access){
            $form = $this->createForm(UserFilesAdminType::class, $ufile);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $ufile->getReceiver()->addUserFile($ufile);
            }
        } else {
            $form = $this->createForm(UserFilesType::class, $ufile);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                foreach($rep->findAllByUserRole(User::MEDIATEUR) as $mediateur){
                    $mediateur->addUserFile($ufile);
                }
            }
        }
        if($form != NULL && $form->isSubmitted() && $form->isValid()){
            $file = $form->get('file')->get('name')->getData();

            if($file != NULL)
            {
                $id = sizeof($this->getDoctrine()->getRepository(UserFiles::class)->findAll())+1;
                $fileName = str_replace(" ","-",$form->get('file')->getData()->getTitle());
                $fileName .= $id.'.'.$file->guessExtension();

                $file->move(
                    $this->getParameter('file_directory'),
                    $fileName
                );
                $ufile->getFile()->setName($fileName);
            }

            $user->addSenderFile($ufile);
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le fichier a bien été envoyé.'
            );
        }

        return $this->render('user/show_files.html.twig', [
            'user' => $user,
            'sending' => $sending,
            'form' => $form->createView(),
            'access' => $access
        ]);
    }

    /**
     * Change status to a specific file (not "important")
     * @Route("user/fichiers/{id}/editStatus", name="edit_status_file")
     * @param FileUser      $file
     * @param ObjectManager $manager
     * @return Response
     */
    public function editStatusFileUser(UserFiles $ufile, ObjectManager $manager)
    {
        $ufile->setImportant(false);
        $manager->persist($ufile);
        $manager->flush();
        return $this->redirectToRoute('user_show_file');
    }

    /**
     * Delete a file by a sender only
     * @Route("user/fichiers/{id}/delete", name="delete_file")
     * @param Files         $file
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteFileUser(Files $file, ObjectManager $manager)
    {
        $rep = $this->getDoctrine()->getRepository(UserFiles::class);
        $filename = $this->getParameter('file_directory') . "/" . $file->getName();
        $filesystem = new Filesystem();
        $filesystem->remove($filename);

        $ufiles = $rep->findBy([
            'sender' => $this->getUser(),
            'file' => $file
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

        return $this->redirectToRoute('user_show_file');
    }

    /**
     * Download a specific file
     * @Route("user/fichiers/{id}", name="download_file")
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
}
