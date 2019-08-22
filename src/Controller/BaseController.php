<?php

namespace App\Controller;

use App\Controller\OtherController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BaseController extends OtherController
{
    /**
     * Show all users weather (only current promotion users)
     * @Route("/users/weather", name="users_weather")
     * @param UserRepository $rep
     * @return Response
     */
    public function usersWeather(UserRepository $rep)
    {
        return $this->render('weather/all.html.twig', [
            'users' => $rep->findAllByCurrentPromo()
        ]);
    }


    /**
     * Show a weather modal every day with random questions and persist the "user weather"
     * @Route("weather", name="weather")
     * @param Request       $req
     * @param ObjectManager $manager
     * @return Response
     */
    public function weather(Request $request, ObjectManager $manager)
    {
        $user = $this->getUser();

        // Check last connected date and now
        $today = new \DateTime();
        $lastConnect = $user->getLastConnect();
        $interval = date_diff($lastConnect, $today);

        if($interval->format('%R%a') > 0){
            $updated = 0;

            // Choose a random question
            $index = mt_rand(0, sizeof($this->questions)-1);
            $question = array_keys($this->questions)[$index];

            $form = $this->createFormBuilder($user)
                ->setAction($this->generateUrl('weather'))
                ->add('weather', ChoiceType::class, [
                    'choices' => $this->questions[$question],
                    'expanded' => true,
                    'multiple' => false
                ])
                ->getForm();
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                $user->setLastConnect(new \DateTime);
                $manager->persist($user);
                $manager->flush();

                return $this->redirectToRoute('home');
            }

            $render = [
                'user'     => $user,
                'updated'  => $updated,
                'question' => $question,
                'form'     => $form->createView()
            ];
        } else {
            $updated = 1;

            $render = [
                'user'    => $user,
                'updated' => $updated
            ];
        }
        return $this->render('weather/modal.html.twig', $render);
    }
}
