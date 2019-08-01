<?php

namespace App\Controller;

use App\Entity\User;
use App\Controller\OtherController;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends OtherController
{
    /**
     * Home page
     * @Route("/", name="home")
     * @return Response
     */
    public function home(UserRepository $rep)
    {
        return $this->render('home/index.html.twig', [
            'date'  => new \DateTime(),
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
            $updated = false;

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
            $updated = true;

            $render = [
                'user'    => $user,
                'updated' => $updated
            ];
        }
        return $this->render('weather/modal.html.twig', $render);
    }
}
