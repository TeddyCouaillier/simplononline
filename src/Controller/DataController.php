<?php

namespace App\Controller;

use App\Entity\Data;
use App\Entity\User;
use App\Entity\UserData;
use App\Form\Data\DataType;
use App\Repository\DataRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/donnees", name="data_")
 */
class DataController extends AbstractController
{
    /**
     * Show all datas
     * @Route("/all", name="all")
     * @IsGranted("ROLE_FORMER")
     * @param Request        $request
     * @param ObjectManager  $manager
     * @param DataRepository $rep
     * @return Response
     */
    public function showAllDatas(Request $request, ObjectManager $manager, DataRepository $rep)
    {
        $data = new Data();
        $urep = $this->getDoctrine()->getRepository(User::class);
        $form = $this->createForm(DataType::class,$data);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            foreach($urep->findAll() as $user){
                $udata = new UserData();
                $udata->setData($data)
                      ->setUser($user);
                $manager->persist($udata);
            }
            $manager->persist($data);
            $manager->flush();

            $this->addFlash(
                'success',
                'La donnée a bien été ajoutée.'
            );
        }

        return $this->render('data/all.html.twig',[
            'datas' => $rep->findAll(),
            'users' => $this->getDoctrine()->getRepository(User::class)->findAllByCurrentPromo(),
            'form'  => $form->createView()
        ]);
    }

    /**
     * Delete a specific data
     * @Route("/{id}/delete", name="delete")
     * @IsGranted("ROLE_ADMIN")
     * @param Data $data
     * @param ObjectManager $manager
     * @return Response
     */
    public function deleteData(Data $data, ObjectManager $manager)
    {
        $manager->remove($data);
        $manager->flush();

        $this->addFlash(
            'success',
            'La donnée a bien été supprimée.'
        );

        return $this->redirectToRoute('admin_all_datas');
    }
}
