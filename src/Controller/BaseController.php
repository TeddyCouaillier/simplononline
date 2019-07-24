<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BaseController extends AbstractController
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
