<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Home page
     * @Route("/", name="home")
     * @return Response
     */
    public function home()
    {

        return $this->render('home/index.html.twig', [
            'date' => new \DateTime(),
        ]);
    }
}
