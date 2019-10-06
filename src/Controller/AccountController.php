<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserNotif;
use App\Entity\PasswordUpdate;
use App\Form\User\EditAvatarType;
use App\Form\User\EditUserType;
use App\Form\User\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/", name="account_")
 */
class AccountController extends AbstractController
{
    /**
     * User log in
     * @Route("/login", name="login")
     * @param AuthenticationUtils $utils
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        return $this->render('other/login.html.twig',[
            'error'    => $utils->getLastAuthenticationError(),
            'username' => $utils->getLastUsername()
        ]);
    }

    /**
     * User log out
     * @Route("/deconnexion", name="logout")
     * @return void
     */
    public function logout() {}

    /**
     * Edit the current user
     * @Route("/account/modifier", name="edit")
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editAccount(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR') && $this->getUser() == null){
            throw new AccessDeniedHttpException();
        }

        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();

        $formUsr = $this->createForm(EditUserType::class, $user);
        $formImg = $this->createForm(EditAvatarType::class, $user);
        $formPwd = $this->createForm(PasswordUpdateType::class, $passwordUpdate);

        $formUsr->handleRequest($request);
        $formPwd->handleRequest($request);
        $formImg->handleRequest($request);

        if($formImg->isSubmitted() && $formImg->isValid()){
            $image = $formImg->get('avatar')->getData();
            if($image != NULL)
            {
                $imageName = $user->getAvatarName().'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('image_directory'),
                    $imageName
                );
                $user->setAvatar($imageName);
            }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'L\'utilisateur a bien été mis à jour.'
            );

            return $this->redirectToRoute('account_show', ['slug'=> $user->getSlug()]);
        }

        if($formPwd->isSubmitted() && $formPwd->isValid())
        {
            if(!$encoder->isPasswordValid($user,$passwordUpdate->getOldPassword())){
                $formPwd->get('oldPassword')->addError(new FormError('Le mot de passe actuel n\'est pas le bon'));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $user->setPassword($encoder->encodePassword($user,$newPassword));

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien été modifié'
                );

                return $this->redirectToRoute('account_show',['id' => $user->getId()]);
            }
        }

        if($formUsr->isSubmitted() && $formUsr->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'L\'utilisateur a bien été mis à jour'
            );

            return $this->redirectToRoute('account_show',['id' => $user->getId()]);
        }

        return $this->render('user/edit.html.twig', [
            'formUsr' => $formUsr->createView(),
            'formPwd' => $formPwd->createView(),
            'formImg' => $formImg->createView(),
            'user'    => $user
        ]);
    }

    /**
     * Edit the current user's password
     * @Route("/account/password-update", name="password")
     * @param Request                      $request
     * @param ObjectManager                $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function updatePassword(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $passwordUpdate = new PasswordUpdate();
        $user = $this->getUser();
        $form = $this->createForm(PasswordUpdateType::class, $passwordUpdate);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            if(!$encoder->isPasswordValid($user,$passwordUpdate->getOldPassword())){
                $form->get('oldPassword')->addError(new FormError('Le mot de passe actuel n\'est pas le bon'));
            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $user->setPassword($encoder->encodePassword($user,$newPassword));

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'Votre mot de passe a bien été modifié'
                );

                return $this->redirectToRoute('account_show',['id' => $user->getId()]);
            }
        }

        return $this->render('user/_form_password.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Delete a specific user's notification
     * @Route("/notification/{id}/supprimer", name="delete_notif")
     * @param UserNotif     $unotif
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteUserNotif(UserNotif $unotif, ObjectManager $manager)
    {
        $manager->remove($unotif);
        $manager->flush();

        $this->addFlash(
            'success',
            'La notification a bien été supprimée.'
        );

        return $this->redirectToRoute('account_show_notif',['slug' => $this->getUser()->getSlug()]);
    }

    /**
     * Show all user's notifications
     * @Route("/notification", name="show_notif")
     * @return Response
     */
    public function showUserNotif()
    {
        return $this->render('other/show_notif.html.twig',[
            'user' => $this->getUser()
        ]);
    }

    /**
     * Delete all user's notifications
     * @Route("/notification/supprimer", name="delete_all_notif")
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteAllNotif(ObjectManager $manager)
    {
        foreach($this->getUser()->getNotifReceived() as $unotif){
            $manager->remove($unotif);
        }
        $manager->flush();

        $this->addFlash(
            'success',
            'Les notifications ont bien été supprimées.'
        );

        return $this->redirectToRoute('account_show_notif',['slug' => $this->getUser()->getSlug()]);
    }

    /**
     * Show a specific user
     * @Route("/account", name="show")
     * @Route("/account/{slug}", name="user_show")
     * @param User          $user
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function showUser(User $user = null, Request $request, ObjectManager $manager)
    {
        if($user == null){
            $user = $this->getUser();
        }
        if($user == $this->getUser() && ($this->isGranted(User::FORMER) || $this->isGranted(User::MEDIATEUR))){
            return $this->redirectToRoute('admin_account');
        }
        if($request->query->get('seen') != null){
            $unotif = $this->getDoctrine()->getRepository(UserNotif::class)->find($request->query->get('seen'));
            if($unotif != null){
                $unotif->setSeen(true);
                $manager->persist($unotif);
                $manager->flush();
            }
        }

        if($user != $this->getUser()){
            return $this->render('user/show.html.twig', [
                'user' => $user
            ]);
        }

        return $this->render('user/account.html.twig', [
            'user' => $user,
            'date' => new \DateTime()
        ]);
    }
}
