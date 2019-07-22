<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\EditUserType;
use App\Form\User\CreateUserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/user", name="user_")
 */
class UserController extends AbstractController
{
    /**
     * Create an user
     * @Route("/create", name="create")
     * @param Request $request
     * @param ObjectManager $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function createUser(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($encoder->encodePassword($user, 'test'));
            $user->setAvatar('avatar.png');

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'L\'utilisateur a bien été créé.'
            );
            return $this->redirectToRoute("user_show",['id' => $user->getId()]);
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit the current user
     * @Route("/edit/{id}", name="edit")
     * @param User $user
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editUserAccount(User $user, Request $request, ObjectManager $manager){
        $imageName = "";

        $currentAvatar = $user->getAvatar();
        if(!empty($currentAvatar)){
            $imageName = $user->getAvatar();
        }
        $form = $this->createForm(EditUserType::class, $user);

        $form->handleRequest($request);
        if($form->isSubmitted()){
            if(!$form->isValid()){
                $user->setAvatar($imageName);
            } else {
                $image = $form->get('avatar')->getData();
                if($image != NULL)
                {
                    $imageName = $user->getAvatarName().'.'.$image->guessExtension();
                    $image->move(
                        $this->getParameter('image_directory'),
                        $imageName
                    );
                    $user->setAvatar($imageName);
                } else {
                    $user->setAvatar($imageName);
                }

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'L\'utilisateur a bien été mis à jour.'
                );
                return $this->redirectToRoute('user_show', ['id'=> $user->getId()]);
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Show all users
     * @Route("/all", name="all")
     * @param UserRepository $rep
     * @return Response
     */
    public function allUsers(UserRepository $rep)
    {
        return $this->render('user/all.html.twig', [
            'users' => $rep->findAll()
        ]);
    }

    /**
     * Show a specific user
     * @Route("/{id}", name="show")
     * @param User $user
     * @return Response
     */
    public function showUser(User $user)
    {
        return $this->render('user/show.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * Delete a specific user
     * @Route("/{id}/delete", name="delete")
     * @param User $user
     * @param ObjectManager $manager
     * @return void
     */
    public function deleteUser(User $user, ObjectManager $manager)
    {
        $manager->remove($user);
        $manager->flush();
        return $this->redirectToRoute('user_all');
    }
}
