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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/donnees", name="data_")
 */
class DataController extends AbstractController
{
    /**
     * Delete a specific data
     * @Route("/{id}/delete", name="delete")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
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
