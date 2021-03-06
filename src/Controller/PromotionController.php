<?php

namespace App\Controller;

use App\Entity\Promotion;
use App\Repository\UserRepository;
use App\Repository\PromotionRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/promotion", name="promo_")
 */
class PromotionController extends AbstractController
{
    /**
     * Show all promotions
     * @Route("/tout", name="all")
     * @param PromotionRepository   $rep
     * @return Response
     */
    public function allPromo(PromotionRepository $rep)
    {
        return $this->render('promotion/all.html.twig', [
            'promos' => $rep->findAll()
        ]);
    }

    /**
     * Show all users without a promotion
     * @Route("/autres", name="other")
     * @param UserRepository      $rep
     * @param PromotionRepository $rep
     * @return Response
     */
    public function allOthersPromo(UserRepository $rep, PromotionRepository $prep)
    {
        $others = $rep->findBy(['promotion'=> null]);
        $users = [];
        foreach($others as $other){
            if(sizeof($other->getRoles()) <= 1){
                $users[] = $other;
            }
        }
        return $this->render('promotion/other.html.twig', [
            'users' => $users,
            'promos'=> $prep->findAll()
        ]);
    }

    /**
     * Show a specific promotion
     * @Route("/{slug}", name="show")
     * @param Promotion           $promo promotion to show
     * @param UserRepository      $rep
     * @param PromotionRepository $prep
     * @return Response
     */
    public function showPromo(Promotion $promo, UserRepository $rep, PromotionRepository $prep)
    {
        return $this->render('promotion/show.html.twig', [
            'promo' => $promo,
            'promos'=> $prep->findAll(),
            'users' => $rep->findAllByPromoDesc($promo),
            'modo'  => $rep->findAllModeratorByPromo($promo)
        ]);
    }
}
