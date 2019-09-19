<?php

namespace App\Controller;

use App\Entity\Help;
use App\Entity\User;
use App\Entity\Brief;
use App\Entity\Language;
use App\Entity\Promotion;
use App\Form\Help\HelpType;
use App\Repository\HelpRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BaseController extends AbstractController
{
    /**
     * @Route("/accueil", name="home")
     */
    public function home()
    {
        $prep = $this->getDoctrine()->getRepository(Promotion::class);
        $brep = $this->getDoctrine()->getRepository(Brief::class);
        return $this->render('home/index.html.twig', [
            'promos' => $prep->findAll(),
            'briefs' => $brep->findAll()
        ]);
    }

    /**
     * Show a specific user in the home page
     * @Route("/accueil/apprenant/{slug}", name="home_user")
     * @param User $user
     * @return Response
     */
    public function homeUsers(User $user)
    {
        return $this->render('home/user.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Show a specific brief in the home page
     * @Route("/accueil/brief/{slug}", name="home_brief")
     * @param Brief $brief
     * @return Response
     */
    public function homeBrief(Brief $brief)
    {
        return $this->render('home/brief.html.twig', [
            'brief' => $brief
        ]);
    }

    /**
     * Delete a specific help link
     * @Route("aides/{id}/supprimer", name="help_delete")
     * @param Help          $help
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteHelp(Help $help, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() != $help->getPublisher()){
            throw new AccessDeniedException();
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
     * @Route("aides/liens", name="help_show")
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

    /**
     * Show all users weather (only current promotion users)
     * @Route("/utilisateurs/meteo", name="users_weather")
     * @param UserRepository $rep
     * @return Response
     */
    public function usersWeather(UserRepository $rep)
    {
        return $this->render('weather/all.html.twig', [
            'formers' => $rep->findAllModeratorByCurrentPromo(),
            'users'   => $rep->findAllByCurrentPromo()
        ]);
    }

    /**
     * Edit the current user weather
     * @Route("/meteo/edit", name="weather_edit")
     * @param Request       $request
     * @param ObjectManager $manager
     * @return JsonResponse
     */
    public function editWeather(Request $request, ObjectManager $manager)
    {
        $type = $request->request->get('type');
        $user = $this->getUser();
        $update = false;
        if($type >= 1 && $type <= 5){
            $user->setWeather($type);
            $manager->persist($user);
            $manager->flush();
            $update = true;
        }

        $response = [ 'update' => $update ];

        return new JsonResponse($response);
    }

    /**
     * Update the current promo's users weather
     * @Route("/meteo/update", name="weather")
     * @param ObjectManager  $manager
     * @param UserRepository $rep
     * @return Response
     */
    public function updateWeather(ObjectManager $manager, UserRepository $rep)
    {
        $users = $rep->findAllWeather();

        if(count($users) > 0){
            foreach($users as $user){
                if($user->getWeather() != 0){
                    $user->setWeather(0)
                         ->setLastConnect(new \DateTime());
                    $manager->persist($user);
                }
            }
            $manager->flush();
            return $this->redirectToRoute('user_account');
        }
        return new Response();
    }
}
