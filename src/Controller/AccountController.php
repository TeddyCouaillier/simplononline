<?php

namespace App\Controller;

use App\Entity\PasswordUpdate;
use App\Form\User\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("", name="account_")
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
        return $this->render('account/login.html.twig',[
            'hasError' => $utils->getLastAuthenticationError() !== null,
            'username' => $utils->getLastUsername()
        ]);
    }

    /**
     * User log out
     * @Route("/logout", name="logout")
     * @return void
     */
    public function logout() {}

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

                return $this->redirectToRoute('user_show',['id' => $user->getId()]);
            }
        }

        return $this->render('user/update_password.html.twig',[
            'form' => $form->createView()
        ]);
    }
}
