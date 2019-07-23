<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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
