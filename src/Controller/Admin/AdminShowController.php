<?php

namespace App\Controller\Admin;

use App\Entity\Data;
use App\Entity\Help;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Files;
use App\Entity\Skills;
use App\Entity\Project;
use App\Entity\Language;
use App\Entity\UserData;
use App\Entity\Promotion;
use App\Entity\Correction;
use App\Entity\UserSkills;
use App\Form\Data\DataType;
use App\Form\Help\HelpType;
use App\Service\Pagination;
use App\Form\Skill\SkillType;
use App\Form\File\FilesAdminType;
use App\Form\User\CreateUserType;
use App\Repository\HelpRepository;
use App\Repository\UserRepository;
use App\Repository\SkillsRepository;
use App\Form\Promotion\PromotionType;
use App\Repository\ProjectRepository;
use App\Form\Project\AddCorrectionType;
use App\Repository\PromotionRepository;
use App\Repository\CorrectionRepository;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin", name="admin_")
 */
class AdminShowController extends AbstractController
{
    /**
     * Admin home page
     * @Route("/home", name="home")
     * @param UserRepository $rep
     * @return Response
     */
    public function adminHome(UserRepository $rep)
    {
        $file = new Files();
        $form = $this->createForm(FilesAdminType::class, $file);
        return $this->render('admin/index.html.twig', [
            'users' => $rep->findAllByCurrentPromo(),
            'form'  => $form->createView()
        ]);
    }

    /**
     * Show all users (all or by promotion) + adding user form
     * @Route("/users/{slug}/{page<\d+>?1}", name="all_users")
     * @param string     $slug promo search (all, other, specific promo)
     * @param integer    $page       current page
     * @param Request    $request
     * @param Pagination $pagination pagination service
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function allUsers(string $slug = "all", int $page, Request $request, Pagination $pagination, UserPasswordEncoderInterface $encoder)
    {
        $manager = $this->getDoctrine()->getManager();
        $prep    = $this->getDoctrine()->getRepository(Promotion::class);

        $promo = $prep->findOneBy(['slug' => $slug]);
        if($slug != "all" && $slug != "other" && $promo == null){
            throw new Exception("Route problem");
        }

        $pagination->setEntity(User::class)
                   ->setLimit(26)
                   ->setPage($page);

        if($slug == 'other'){
            $pagination->setCriteria(['promotion' => null]);
        } else if($slug == 'all') {
            $pagination->setCriteria([]);
        } else {
            $pagination->setCriteria(['promotion' => $promo]);
        }

        $user = new User();
        $form = $this->createForm(CreateUserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $role_id = $request->request->get('create_user')['userRoles'];
            if($role_id !== null){
                $rrole = $this->getDoctrine()->getRepository(Role::Class);
                $role = $rrole->find($role_id);
                if($role != null){
                    $role->addUser($user);
                    $user->setPromotion(null);
                }
            }

            $user->setPassword($encoder->encodePassword($user, 'test'));
            $user->setAvatar('avatar.png');

            $skills = $this->getDoctrine()->getRepository(Skills::class)->findAll();
            $datas  = $this->getDoctrine()->getRepository(Data::class)->findAll();

            $user->initializeSkills($skills);
            $user->initializeDatas($datas);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'L\'utilisateur a bien été créé.'
            );
        }

        return $this->render('admin/all_users.html.twig', [
            'pagination' => $pagination,
            'promo'      => $promo,
            'slug'       => $slug,
            'promotions' => $prep->findAll(),
            'form'       => $form->createView()
        ]);
    }

    /**
     * Show all promotions + adding promo form
     * @Route("/promotions", name="all_promo")
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

    /**
     * Show users without a promo
     * @Route("/promotions/autres", name="show_promo_others")
     * @param UserRepository $rep
     * @return Response
     */
    public function showPromotionOther(UserRepository $rep)
    {
        $others = $rep->findBy(['promotion'=> null]);
        $users = [];
        foreach($others as $other){
            if(sizeof($other->getRoles()) <= 1){
                $users[] = $other;
            }
        }
        return $this->render('admin/show_promo.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * Show a specific promo
     * @Route("/promotions/{slug}", name="show_promo")
     * @param Promotion $promo
     * @return void
     */
    public function showPromotion(Promotion $promo)
    {
        return $this->render('admin/show_promo.html.twig', [
            'promo' => $promo,
        ]);
    }

    /**
     * Show all skills + adding skill form
     * @Route("/competences", name="all_skills")
     * @param Request          $request
     * @param ObjectManager    $manager
     * @param SkillsRepository $rep
     * @return Response
     */
    public function allSkills(Request $request, ObjectManager $manager, SkillsRepository $rep)
    {
        $skill = new Skills();
        $form = $this->createForm(SkillType::class, $skill);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($skill);
            $users = $this->getDoctrine()->getRepository(User::class)->findAll();
            foreach($users as $user){
                $uskill = new UserSkills();
                $uskill->setSkill($skill)
                       ->setUser($user)
                       ->setLevel(0);
                $manager->persist($uskill);
            }

            $manager->flush();

            $this->addFlash(
                'success',
                'La promotion a bien été ajoutée.'
            );
        }

        return $this->render('admin/all_skills.html.twig',[
            'skills' => $rep->findAll(),
            'form'   => $form->createView()
        ]);
    }

    /**
     * Show all projects
     * @Route("/projets/{page<\d+>?1}", name="all_projects")
     * @param ProjectRepository $rep
     * @return Response
     */
    public function allProjects(Pagination $pagination, $page)
    {
        $pagination->setEntity(Project::class)
                   ->setPage($page);

        return $this->render('admin/all_projects.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Show all trainings courses
     * @Route("/stages", name="all_trainings")
     * @param UserRepository $rep
     * @return Response
     */
    public function allTrainings(UserRepository $rep)
    {
        return $this->render('admin/all_trainings.html.twig', [
            'users' => $rep->findAllByCurrentPromo()
        ]);
    }

    /**
     * Show all datas + adding data form + adding language form
     * @Route("/donnees", name="all_datas")
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function allDatas(Request $request, ObjectManager $manager)
    {
        $drep = $this->getDoctrine()->getRepository(Data::class);
        $lrep = $this->getDoctrine()->getRepository(Language::class);
        $urep = $this->getDoctrine()->getRepository(User::class);

        $language = new Language();
        $data     = new Data();
        $formLanguage =
            $this->createFormBuilder($language)
                 ->add('label',null,[ 'attr' => ['placeholder' => 'HTML, PHP, Symfony...']])
                 ->getForm();
        $formData = $this->createForm(DataType::class, $data);

        $formLanguage->handleRequest($request);
        $formData->handleRequest($request);
        if($formLanguage->isSubmitted() && $formLanguage->isValid()){
            $manager->persist($language);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le langage a bien été ajouté.'
            );
        }
        else if($formData->isSubmitted() && $formData->isValid()){
            $manager->persist($data);
            foreach($urep->findAll() as $user){
                $udata = new UserData();
                $udata->setData($data)
                      ->setUser($user);
                $manager->persist($udata);
            }
            $manager->flush();

            $this->addFlash(
                'success',
                'La donnée a bien été ajoutée.'
            );
        }

        return $this->render('admin/all_datas.html.twig', [
            'datas'     => $drep->findAll(),
            'languages' => $lrep->findAll(),
            'roles'     => $urep->findAllUserByRole(),
            'formLanguage' => $formLanguage->createView(),
            'formData'     => $formData->createView()
        ]);
    }

    /**
     * Show all helps + adding help form
     * @Route("/aides/{page<\d+>?1}", name="all_helps")
     * @param Request        $request
     * @param ObjectManager  $manager
     * @param HelpRepository $rep
     * @return Response
     */
    public function allHelps(Request $request, ObjectManager $manager, Pagination $pagination, int $page)
    {
        $pagination->setEntity(Help::class)
                   ->setPage($page);

        $help = new Help();
        $form = $this->createForm(HelpType::class, $help);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->getUser()->addHelp($help);
            $manager->persist($this->getUser());
            $manager->flush();

            $this->addFlash(
                'success',
                'Le lien a bien été ajouté.'
            );
        }

        return $this->render('admin/all_helps.html.twig', [
            'pagination' => $pagination,
            'form'  => $form->createView()
        ]);
    }

    /**
     * @Route("/corrections/{page<\d+>?1}", name="all_corrections")
     *
     * @param CorrectionRepository $rep
     * @return void
     */
    public function allCorrections(Pagination $pagination, Request $request, ObjectManager $manager, int $page)
    {
        $pagination->setEntity(Correction::class)
                   ->setPage($page);

        $correction = new Correction();
        $form = $this->createForm(AddCorrectionType::class, $correction);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($correction);
            $manager->flush();

            $this->addFlash(
                'success',
                'La correction a bien été ajoutée.'
            );
        }
        return $this->render('admin/all_corrections.html.twig', [
            'form'        => $form->createView(),
            'pagination' => $pagination
        ]);
    }
}