<?php

namespace App\Controller;

use App\Entity\Help;
use App\Entity\User;
use App\Form\Help\HelpType;
use App\Repository\HelpRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use App\Entity\Language;

/**
 * @Route("/help", name="help_")
 */
class HelpController extends AbstractController
{
    /**
     * Delete a specific help link
     * @Route("/{id}/delete", name="delete")
     * @param Help          $help
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteHelp(Help $help, ObjectManager $manager)
    {
        if(!$this->getUser()->checkRole(User::ADMIN) && $this->getUser() != $help->getPublisher()){
            throw new AccessDeniedHttpException();
        }
        $manager->remove($help);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le lien a bien été supprimé.'
        );

        return $this->redirectToRoute('help_show');
    }

    /**
     * Show all help links and adding form
     * @Route("", name="show")
     * @param ObjectManager  $manager
     * @param Request        $request
     * @param HelpRepository $rep
     * @return Response
     */
    public function showHelp(ObjectManager $manager, Request $request, HelpRepository $rep)
    {
        $help = new Help();
        $form = $this->createForm(HelpType::class, $help);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->getUser()->addHelp($help);
            $manager->persist($this->getUser());
            $manager->flush();

            $this->addFlash(
                'success',
                'Le lien a bien été ajouté.'
            );
        }

        $languages = $this->getDoctrine()->getRepository(Language::class)->findAll();

        return $this->render('help/show.html.twig', [
            'form'      => $form->createView(),
            'helps'     => $rep->findAll(),
            'languages' => $languages
        ]);
    }
}
