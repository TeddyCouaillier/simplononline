<?php

namespace App\Controller;

use App\Entity\Promotion;
use App\Repository\UserRepository;
use App\Form\Promotion\PromotionType;
use App\Repository\PromotionRepository;
use App\Form\Promotion\AddUsersPromotionType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/promotion", name="promo_")
 */
class PromotionController extends AbstractController
{
    /**
     * Show all promotions and add a new promo in a modal
     * @Route("/all", name="all")
     * @param Request               $request
     * @param ObjectManager         $manager
     * @param PromotionRepository   $rep
     * @return Response
     */
    public function allPromo(Request $request, ObjectManager $manager, PromotionRepository $rep)
    {
        $promo = new Promotion();
        $form = $this->createForm(PromotionType::class, $promo);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($promo->getCurrent() == true){
                foreach($rep->findAllOtherCurrent($promo) as $currentPromo){
                    $currentPromo->setCurrent(false);
                }
            }

            $manager->persist($promo);
            $manager->flush();

            $this->addFlash(
                'success',
                'La promotion a bien été ajoutée.'
            );
        }

        return $this->render('promotion/all.html.twig', [
            'promos' => $rep->findAll(),
            'form'   => $form->createView()
        ]);
    }

    /**
     * Show all users without a promotion
     * @Route("/autres", name="other")
     * @param UserRepository $rep
     * @return Response
     */
    public function allOthersPromo(UserRepository $rep)
    {
        $others = $rep->findBy(['promotion'=> null]);
        $users = [];
        foreach($others as $other){
            if(sizeof($other->getRoles()) <= 1){
                $users[] = $other;
            }
        }
        return $this->render('promotion/other.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * Add/Remove users from a promotion
     * @Route("/{slug}/edit-users", name="edit_users")
     * @IsGranted("ROLE_ADMIN")
     * @param Promotion                     $promo    promotion to edit
     * @param Request                       $request
     * @param ObjectManager                 $manager
     * @param UserPasswordEncoderInterface  $encoder
     * @return Response
     */
    public function editUsersPromo(Promotion $promo, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(AddUsersPromotionType::class,$promo);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            foreach($promo->getUsers() as $user){
                $user->setPromotion($promo)
                     ->setPassword($encoder->encodePassword($user, 'test'));

                $manager->persist($user);
            }
            $manager->flush();

            $this->addFlash(
                'success',
                'La promotion a bien été modifiée.'
            );
            return $this->redirectToRoute('promo_show',['slug'=> $promo->getSlug()]);
        }
        return $this->render('promotion/edit.html.twig',[
            'form'  => $form->createView(),
            'promo' => $promo
        ]);
    }

    /**
     * Ajax calling to edit a promo
     * @Route("/{slug}/edit", name="edit")
     * @param Promotion $promo
     * @param Request $request
     * @param ObjectManager $manager
     * @return JsonResponse/Response
     */
    public function editPromo(Promotion $promo, Request $request, ObjectManager $manager, PromotionRepository $rep)
    {
        $form = $this->createForm(PromotionType::class, $promo);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            if($promo->getCurrent() == true){
                foreach($rep->findAllOtherCurrent($promo) as $currentPromo){
                    $currentPromo->setCurrent(false);
                }
            }

            $manager->persist($promo);
            $manager->flush();

            $this->addFlash(
                'success',
                'La promotion a bien été modifiée.'
            );

            return $this->redirectToRoute('promo_all');
        }

        $render = $this->render('promotion/_edit_promo.html.twig', [
            'form'   => $form->createView(),
            'promo'  => $promo
        ]);

        $response = [
            "code" => 200,
            "render" => $render->getContent()
        ];
        return new JsonResponse($response);
    }

    /**
     * Delete a promotion
     * @Route("/{id}/delete", name="delete")
     * @IsGranted("ROLE_ADMIN")
     * @param Promotion     $promo
     * @param ObjectManager $manager
     * @return Response
     */
    public function deletePromo(Promotion $promo, ObjectManager $manager)
    {
        $manager->remove($promo);
        $manager->flush();

        $this->addFlash(
            'success',
            'La promotion a bien été supprimée.'
        );
        return $this->redirectToRoute('promo_all');
    }

    /**
     * Show a specific promotion
     * @Route("/{slug}", name="show")
     * @param Promotion $promo promotion to show
     * @return Response
     */
    public function showPromo(Promotion $promo)
    {
        return $this->render('promotion/show.html.twig', [
            'promo' => $promo,
        ]);
    }
}
