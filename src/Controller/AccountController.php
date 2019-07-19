<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    /**
     * User log in
     * @Route("/login", name="account_login")
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
     * Permet de se deconnecter
     * @Route("/logout", name="account_logout")
     * @return void
     */
    public function logout() {}
}
