<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\User;
use App\Entity\Vote;
use App\Form\Other\GameType;
use App\Service\Pagination;
use App\Entity\UserDeadline;
use App\Entity\TrainingCourse;
use App\Repository\VoteRepository;
use App\Repository\ProjectRepository;
use App\Repository\TrainingCourseRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Form\TrainingCourse\TrainingCourseUserType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

/**
 * @Route("", name="user_")
 */
class UserController extends AbstractController
{
    // -----------------------------------------------------
    // -- Project section
    // -----------------------------------------------------
    /**
     * Show the user's projects
     * @Route("/{slug}/projets/{page<\d+>?1}", name="show_projects")
     * @param integer           $page
     * @param User              $user
     * @param ProjectRepository $rep
     * @return Response
     */
    public function showUserProjects(int $page, User $user, ProjectRepository $rep)
    {
        $limit = 20;
        $offset = $page * $limit - $limit;
        $projects = $rep->findAllByUserLimit($user, $limit, $offset);
        $total = count($rep->findAllByUser($user));
        $pages = ceil($total / $limit);

        return $this->render('project/all.html.twig', [
            'user'     => $user,
            'page'     => $page,
            'pages'    => $pages,
            'projects' => $projects
        ]);
    }

    // -----------------------------------------------------
    // -- Training course section
    // -----------------------------------------------------
    /**
     * Show the specific user's training courses by the same user
     * Show all users training courses by the admin
     * Show the training courses proposed by the admin for all users
     * @Route("/stages", name="show_training")
     * @Route("/admin/stages/{slug}", name="training_admin")
     * @param User                      $user
     * @param TrainingCourseRepository  $rep
     * @return Response
     */
    public function showTraining(User $user = null)
    {
        if($user == null){
            $user = $this->getUser();
        }
        return $this->render('training_course/show.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Edit the specific user's training courses (add, remove & edit training course)
     * @Route("/{slug}/stages/modifier", name="edit_training")
     * @param User          $user
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editTrainingCourse(User $user, Request $request, ObjectManager $manager)
    {
        if($this->getUser() != $user) {
            throw new AccessDeniedException();
        }
        $form = $this->createForm(TrainingCourseUserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les modifications ont bien été enregistrées.'
            );
            return $this->redirectToRoute('user_show_training');
        }

        return $this->render('training_course/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete all trainings courses posted by a specific user
     * @Route("/{slug}/stages/tout-supprimer", name="delete_all_trainings")
     * @param User          $user
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteAllTrainingCourse(User $user, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() != $user){
            throw new AccessDeniedHttpException();
        }

        foreach($user->getTrainingCourse() as $training){
            $manager->remove($training);
        }
        $manager->flush();

        $this->addFlash(
            'success',
            'Les stages ont bien été supprimés.'
        );

        return $this->redirectToRoute('user_show_training');
    }

    /**
     * Delete a specific training course posted by a specific user
     * @Route("/{slug}/stages/{training_id}/supprimer", name="delete_training")
     * @Entity("training", expr="repository.find(training_id)")
     * @param User           $user
     * @param TrainingCourse $training
     * @param ObjectManager  $manager
     * @return Response
     */
    public function deleteTrainingCourse(User $user, TrainingCourse $training, ObjectManager $manager)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() != $user){
            throw new AccessDeniedHttpException();
        }

        $manager->remove($training);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le stage a bien été supprimé.'
        );

        return $this->redirectToRoute('user_show_training');
    }

    // -----------------------------------------------------
    // -- Data section
    // -----------------------------------------------------
    /**
     * Show the user's data
     * @Route("/donnees", name="data")
     * @param User $user
     * @return Response
     */
    public function showData()
    {
        return $this->render('data/show.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    // -----------------------------------------------------
    // -- Game section
    // -----------------------------------------------------
    /**
     * Show all games + adding form
     * @Route("/games/{page<\d+>?1}", name="game")
     * @param integer       $page
     * @param Pagination    $pagination
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function showGames(int $page, Pagination $pagination, Request $request, ObjectManager $manager)
    {
        $pagination->setEntity(Game::class)
                   ->setLimit(20)
                   ->setPage($page);

        $game = new Game($this->getUser());
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($game);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le jeu a bien été ajouté.'
            );
        }

        return $this->render('game/all.html.twig', [
            'pagination' => $pagination,
            'form'       => $form->createView()
        ]);
    }

    /**
     * Delete a specific game
     * @Route("/games/{id}/delete", name="game_delete")
     * @param Game          $game
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteGame(Game $game, ObjectManager $manager)
    {
        $manager->remove($game);
        $manager->flush();

        $this->addFlash(
            'success',
            'Le jeu a bien été supprimé.'
        );

        return $this->redirectToRoute('user_game');
    }

    /**
     * Edit a specific game
     * @Route("/games/{id}/edit", name="game_edit")
     * @param Game          $game
     * @param Request       $request
     * @param ObjectManager $manager
     * @return JsonResponse/Response
     */
    public function editGame(Game $game, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($game);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le jeu a bien été modifié.'
            );

            return $this->redirectToRoute('user_game');
        }

        $render = $this->render('game/edit.html.twig', [
            'form'      => $form->createView(),
            'game'      => $game
        ]);

        $response = [ "render" => $render->getContent() ];

        return new JsonResponse($response);
    }

    /**
     * Like/Dislike a game
     * @Route("/games/{id}/vote", name="game_vote")
     * @param Game           $game
     * @param Request        $request
     * @param ObjectManager  $manager
     * @param VoteRepository $rep
     * @return JsonResponse
     */
    public function editVote(Game $game, Request $request, ObjectManager $manager, VoteRepository $rep)
    {
        $content = ($request->request->get('content') == 'like');
        $user = $this->getUser();
        $vote = $rep->findOneBy(['game' => $game, 'user' => $user]);

        if($vote == null){
            $vote = new Vote($user,$game, $content);
            $manager->persist($vote);
        } else {
            if($content)
            {
                 $vote->getLikeType() ? $manager->remove($vote) : $vote->setLikeType($content);
            }
            else
            {
                !$vote->getLikeType() ? $manager->remove($vote) : $vote->setLikeType($content);
            }
        }

        $manager->flush();

        $response = [
            'countLike'    => $rep->findByLike($game),
            'countDislike' => $rep->findByDislike($game),
        ];

        return new JsonResponse($response);
    }

    // -----------------------------------------------------
    // -- Deadline section
    // -----------------------------------------------------
    /**
     * Valide a specific deadline
     * @Route("/deadline/{id}/active", name="deadline_active")
     * @param UserDeadline  $udeadline
     * @param ObjectManager $manager
     * @return JsonResponse
     */
    public function valideDeadline(UserDeadline $udeadline, ObjectManager $manager)
    {
        $udeadline->setValidate(true);
        $manager->persist($udeadline);
        $manager->flush();

        return new JsonResponse();
    }

    /**
     * @Route("/deadline/tout", name="deadline")
     * @return Response
     */
    public function allDeadline()
    {
        return $this->render('other/deadline.html.twig');
    }
}
