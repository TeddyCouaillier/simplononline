<?php

namespace App\Controller;

use App\Entity\Brief;
use App\Repository\BriefRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/admin/brief", name="brief_")
 */
class BriefController extends AbstractController
{
    /**
     * Create a new brief
     * @Route("/nouveau", name="new")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function addBrief(Request $request, ObjectManager $manager)
    {
        $brief = new Brief($this->getUser());
        $form = $this->createFormBuilder($brief)
                     ->add('title',null,['attr' => ['placeholder' => 'Libellé du brief']])
                     ->add('link',UrlType::class,['attr' => ['placeholder' => 'Lien additionnel'], 'required' => false])
                     ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($request->request->get('content') != ''){
                $brief->setContent($this->stopInject($request->request->get('content')));
            } else {
                $brief->setContent('Pas de brief');
            }

            $manager->persist($brief);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le brief a bien été ajouté.'
            );

            return $this->redirectToRoute('brief_show',['slug' => $brief->getSlug()]);
        }

        return $this->render('brief/add-edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit a specific brief
     * @Route("/{slug}/modifier", name="edit")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
     * @param Brief         $brief
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editBrief(Brief $brief, Request $request, ObjectManager $manager)
    {
        $form = $this->createFormBuilder($brief)
                     ->add('title',null,['attr' => ['placeholder' => 'Libellé']])
                     ->add('link',UrlType::class,['attr' => ['placeholder' => 'Lien GitHub'], 'required' => false])
                     ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($request->request->get('content') != ''){
                $brief->setContent($this->stopInject($request->request->get('content')));
            } else {
                $brief->setContent('Pas de brief');
            }

            $manager->persist($brief);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le brief a bien été modifié.'
            );

            return $this->redirectToRoute('brief_show',['slug' => $brief->getSlug()]);
        }

        return $this->render('brief/add-edit.html.twig', [
            'form'  => $form->createView(),
            'brief' => $brief,
            'content' => htmlspecialchars_decode(str_replace('`','\`', $brief->getContent()))
        ]);
    }

    /**
     * Delete a specific brief
     * @Route("/{slug}/supprimer", name="delete")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
     * @param Brief         $brief
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteBrief(Brief $brief, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() != $brief->getPublisher()){
            throw new AccessDeniedException();
        }

        $manager->remove($brief);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre brief a bien été supprimé.'
        );
        return $this->redirectToRoute('brief_all');
    }

    /**
     * Show all brief
     * @Route("/tout", name="all")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
     * @param BriefRepository $rep
     * @return Response
     */
    public function allBrief(BriefRepository $rep)
    {
        return $this->render('brief/all.html.twig', [
            'briefs' => $rep->findAll()
        ]);
    }

    /**
     * Show a specific brief
     * @Route("/{slug}", name="show")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
     * @param Brief $brief
     * @return Response
     */
    public function showBrief(Brief $brief)
    {
        return $this->render('brief/brief.html.twig', [
            'brief' => $brief
        ]);
    }

    public function stopInject(string $markdown)
    {
        $html = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $markdown);
        $html = strip_tags($html);
        return $html;
    }
}