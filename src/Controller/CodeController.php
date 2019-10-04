<?php

namespace App\Controller;

use App\Entity\Codeblock;
use App\Form\Other\CodeblockType;
use App\Repository\LanguageRepository;
use App\Repository\CodeblockRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/aides/code", name="code_")
 */
class CodeController extends AbstractController
{
    /**
     * Create a new help code
     * @Route("/ajouter", name="add")
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function newCode(Request $request, ObjectManager $manager)
    {
        $code = new Codeblock($this->getUser());
        $form = $this->createForm(CodeblockType::class, $code);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($request->request->get('content') != ''){
                $code->setContent($request->request->get('content'));
            } else {
                $code->setContent('Pas de code');
            }
            $manager->persist($code);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le code a bien été ajouté'
            );

            return $this->redirectToRoute('code_show',['slug' => $code->getSlug()]);
        }

        return $this->render('help/add-edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit a specific help code
     * @Route("/{slug}/modifier", name="edit")
     * @param Codeblock     $code
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editCode(Codeblock $code, Request $request, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() != $code->getPublisher()){
            throw new AccessDeniedException();
        }

        $form = $this->createForm(CodeblockType::class, $code);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($request->request->get('content') != ''){
                $code->setContent($request->request->get('content'));
            } else {
                $code->setContent('Pas de code');
            }
            $manager->persist($code);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le code a bien été ajouté'
            );

            return $this->redirectToRoute('code_all');
        }

        return $this->render('help/add-edit.html.twig', [
            'form' => $form->createView(),
            'code' => $code,
            'content' => htmlspecialchars_decode(str_replace('`','\`', $code->getContent()))
        ]);
    }

    /**
     * Show all help codes
     * @Route("/", name="all")
     * @param CodeblockRepository $rep
     * @param LanguageRepository  $lrep
     * @return Response
     */
    public function allCode(CodeblockRepository $rep, LanguageRepository $lrep)
    {
        return $this->render('help/all.html.twig', [
            'codes'     => $rep->findAll(),
            'languages' => $lrep->findAll()
        ]);
    }

    /**
     * Delete a specific code
     * @Route("/{slug}/delete", name="delete")
     * @param Codeblock     $code
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteCode(Codeblock $code, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() != $code->getPublisher()){
            throw new AccessDeniedException();
        }

        $manager->remove($code);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le code a bien été supprimé'
        );

        return $this->redirectToRoute('code_all');
    }

    /**
     * Show a specific help code
     * @Route("/{slug}", name="show")
     * @param Code $code
     * @return Response
     */
    public function showCode(Codeblock $code)
    {
        return $this->render('help/code.html.twig', [
            'code' => $code
        ]);
    }
}