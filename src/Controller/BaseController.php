<?php

namespace App\Controller;

use App\Entity\Promotion;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        $prep = $this->getDoctrine()->getRepository(Promotion::class);
        return $this->render('home/index.html.twig', [
            'promos' => $prep->findAll()
        ]);
    }

    /**
     * @Route("/apprenant/{slug}", name="home_user")
     *
     * @param User $user
     * @return void
     */
    public function homeUsers(User $user)
    {
        return $this->render('home/user.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Show all users weather (only current promotion users)
     * @Route("/users/weather", name="users_weather")
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

                return $this->redirectToRoute('user_show', ['slug' => $user->getSlug()]);
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

    protected $questions = array(
        "Que penses-tu du code aujourd'hui ?" => array(
            'Je préfère la piscine' => User::RAIN,
            'C\'est de la merde' => User::THUNDER,
            'J\'adore!' => User::SUN,
            'I don\'t care' => User::CLOUD,
            'Petite bière ?' => User::SUNCLOUD,
        ),
        "Comment tu vas ?" => array(
            'Ca peut aller' => User::SUNCLOUD,
            'Bof' => User::CLOUD,
            'Laissez moi mourir' => User::THUNDER,
            'Non !' => User::RAIN,
            'Ca va' => User::SUN,
        ),
        "As-tu des envies de meurtre aujourd'hui ?" => array(
            'Ca dépends de la victime' => User::RAIN,
            'Pas du tout !' => User::SUN,
            'Peut-être...' => User::CLOUD,
            'Ouai !' => User::THUNDER,
            'Pas pour le moment'  => User::SUNCLOUD,
        ),
        "Bien dormi ?" => array(
            'Non !' => User::RAIN,
            'Ca peut aller' => User::SUNCLOUD,
            'Laissez moi mourir' => User::THUNDER,
            'Bof' => User::CLOUD,
            'Ca va' => User::SUN,
        ),
        "Quel est ton envie actuellement ?" => array(
            'De coder <3' => User::SUN,
            'De dormir' => User::RAIN,
            'Petite bière ?' => User::SUNCLOUD,
            'D\'être ce soir' => User::CLOUD,
            'Me jeter par la fenêtre' => User::THUNDER
        ),
        "Aujourd'hui tu es ?" => array(
            'Saitama' => User::SUNCLOUD,
            'Batman' => User::CLOUD,
            'Hannibal Lecter' => User::THUNDER,
            'Thanos' => User::RAIN,
            'Tchoupi' => User::SUN
        ),
        "Aujourd'hui : " => array(
            'Je respire la vie, j\'aime les oiseaux chantant sur une douce brise'  => User::SUN,
            'Je respire la routine, un jour comme un autre.' => User::SUNCLOUD,
            'Je respire la neutralité, je m\'en fous' => User::CLOUD,
            'Je respire la tristesse, je veux partir' => User::RAIN,
            'Je respire la haine, j\'écrase les fleurs sans pitié' => User::THUNDER
        ),
        "Quel est ton émoticone aujourd'hui ?" => array(
            '🔥' => User::SUN,
            '😐' => User::CLOUD,
            '🌈' => User::SUNCLOUD,
            '🧨' => User::RAIN,
            '☠️'  => User::THUNDER
        ),
        "Quel est ton totem aujourd'hui ?" => array(
            '🐷' => User::SUNCLOUD,
            '🦉' => User::CLOUD,
            '🦖' => User::THUNDER,
            '🐙' => User::RAIN,
            '🦄' => User::SUN
        ),
        "Aujourd'hui tu es ?" => array(
            '💊' => User::CLOUD,
            '🍺' => User::SUNCLOUD,
            '💣' => User::THUNDER,
            '🧱' => User::RAIN,
            '🎉' => User::SUN
        ),
    );
}
