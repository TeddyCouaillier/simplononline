<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\User\EditUserType;
use App\Form\User\CreateUserType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * Create an user
     * @Route("/user/create", name="create_user")
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
            return $this->redirectToRoute("show_user",['id' => $user->getId()]);
        }

        return $this->render('user/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit the current user
     * @Route("/user/edit", name="edit_user")
     * @param User $user
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editUser(Request $request, ObjectManager $manager){
        $user = $this->getUser();
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
                return $this->redirectToRoute('show_user', ['id'=> $user->getId()]);
            }
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    /**
     * Show a specific user
     * @Route("/user/{id}", name="show_user")
     * @param User $user
     * @return Response
     */
    public function showUser(User $user)
    {
        return $this->render('user/show.html.twig', [
            'user' => $user
        ]);
    }
}
