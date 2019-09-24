<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Promotion;
use App\Form\Promotion\PromotionType;
use App\Repository\PromotionRepository;
use App\Form\Promotion\EditPromotionType;
use App\Form\Promotion\AddUsersPromotionType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin", name="admin_")
 */
class AdminPromotionController extends AbstractController
{
    /**
     * Edit a specific promotion
     * @Route("/promotion/{id}/modifier", name="promo_edit")
     * @param Promotion     $promo
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response/JsonResponse
     */
    public function editPromo(Promotion $promo, Request $request, ObjectManager $manager)
    {
        $rep = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(EditPromotionType::class, $promo);
        $form->handleRequest($request);
        if($form->isSubmitted()){
            if($form->isValid()){
                $promo->clearModerator();
                if(isset($request->request->get('edit_promotion')['moderators'])){
                    $moderators = $request->request->get('edit_promotion')['moderators'];
                    for($i = 0 ; $i < sizeof($moderators) ; $i++){
                        $moderator = $this->getDoctrine()->getRepository(User::class)->find($moderators[$i]);
                        $promo->addModerator($moderator);
                    }
                }
                $manager->persist($promo);
                $manager->flush();

                $this->addFlash(
                    'success',
                    'La promotion a bien été modifiée.'
                );
            }

            return $this->redirectToRoute('admin_all_promo');
        }


        $render = $this->render('admin/edit.html.twig', [
            'form'       => $form->createView(),
            'promo'      => $promo,
            'moderators' => $rep->findAllUserByRole()
        ]);

        $response = [ "render" => $render->getContent() ];

        return new JsonResponse($response);
    }

    /**
     * Add/Remove users from a promotion
     * @Route("/{slug}/ajouter-apprenants", name="promo_users")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
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
     * Remove a specific user from his promotion
     * @Route("/{slug}/retirer-promotion", name="promo_remove_user")
     * @param User          $user
     * @param ObjectManager $manager
     * @return Response
     */
    public function removeUsersPromo(User $user, ObjectManager $manager)
    {
        $promo = $user->getPromotion();
        $user->setPromotion(null);
        $manager->persist($user);
        $manager->flush();

        $this->addFlash(
            'success',
            'L\'utilisateur a bien été retiré de la promotion'
        );

        return $this->redirectToRoute('promo_show',['slug' => $promo->getSlug()]);
    }

    /**
     * Active a specific promotion and inactive all others promo
     * @Route("/promotions/{id}/activer", name="promo_active")
     * @param Promotion           $promo
     * @param ObjectManager       $manager
     * @param PromotionRepository $rep
     * @return JsonResponse
     */
    public function activePromotion(Promotion $promo, ObjectManager $manager, PromotionRepository $rep)
    {
        foreach($rep->findAll() as $promo_others){
            $promo_others->setCurrent(false);
        }
        $promo->setCurrent(true);
        $manager->persist($promo);
        $manager->flush();

        return new JsonResponse();
    }

    /**
     * Delete a promotion
     * @Route("/{id}/supprimer", name="promo_delete")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
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
        return $this->redirectToRoute('admin_all_promo');
    }

    /**
     * Show all promotions + adding promo form
     * @Route("/promotion/tout", name="all_promo")
     * @param Request             $request
     * @param ObjectManager       $manager
     * @param PromotionRepository $rep
     * @return Response
     */
    public function allPromotions(Request $request, ObjectManager $manager, PromotionRepository $rep)
    {
        $promo = new Promotion();
        $form = $this->createForm(PromotionType::class, $promo);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($promo);
            $manager->flush();

            $this->addFlash(
                'success',
                'La promotion a bien été ajoutée.'
            );
        }

        return $this->render('promotion/all.html.twig', [
            'promos'     => $rep->findAll(),
            'form'       => $form->createView(),
            'admin'      => true
        ]);
    }
}