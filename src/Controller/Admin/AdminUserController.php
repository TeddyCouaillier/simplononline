<?php

namespace App\Controller\Admin;

use Exception;
use App\Entity\Data;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Skills;
use App\Entity\Promotion;
use App\Service\Pagination;
use App\Entity\Notification;
use App\Form\User\EditUserType;
use App\Form\User\CreateUserType;
use App\Form\Data\EditUserDataType;
use App\Form\Skill\EditUserSkillsType;
use App\Form\User\EditAvatarType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/admin", name="admin_")
 */
class AdminUserController extends AbstractController
{
    /**
     * Edit a specific account
     * @Route("/utilisateur/{slug}/modifier", name="user_edit")
     * @param User          $user
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editUserAccount(User $user, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder){
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR')){
            throw new AccessDeniedHttpException();
        }

        $formUsr = $this->createForm(EditUserType::class, $user);
        $formImg = $this->createForm(EditAvatarType::class, $user);
        $formPwd = $this->createFormBuilder($user)
                        ->add('password',PasswordType::class)
                        ->getForm();

        $formUsr->handleRequest($request);
        $formPwd->handleRequest($request);
        $formImg->handleRequest($request);

        if($formImg->isSubmitted() && $formImg->isValid()){
            $image = $formImg->get('avatar')->getData();
            if($image != NULL)
            {
                $imageName = $user->getAvatarName().'.'.$image->guessExtension();
                $image->move(
                    $this->getParameter('image_directory'),
                    $imageName
                );
                $user->setAvatar($imageName);
            }

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'L\'utilisateur a bien été mis à jour.'
            );
        }

        if($formPwd->isSubmitted() && $formPwd->isValid())
        {
            $user->setPassword($encoder->encodePassword($user,$user->getPassword()));

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre mot de passe a bien été modifié'
            );
        }

        if($formUsr->isSubmitted() && $formUsr->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'L\'utilisateur a bien été mis à jour'
            );
        }

        return $this->render('user/edit.html.twig', [
            'formUsr' => $formUsr->createView(),
            'formPwd' => $formPwd->createView(),
            'formImg' => $formImg->createView(),
            'user'    => $user
        ]);
    }

    /**
     * Edit a specific user's password
     * @Route("/utilisateur/{slug}/password-update", name="user_password")
     * @param User                         $user
     * @param Request                      $request
     * @param ObjectManager                $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function editPassword(User $user, Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        if(!$this->isGranted('ROLE_FORMER') && !$this->isGranted('ROLE_MEDIATEUR')){
            throw new AccessDeniedHttpException();
        }

        $form = $this->createFormBuilder($user)
                ->add('password',PasswordType::class)
                ->getForm();

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $user->setPassword($encoder->encodePassword($user,$user->getPassword()));

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Le mot de passe a bien été modifié'
            );

            return $this->redirectToRoute('admin_all_datas');
        }

        return $this->render('admin/editPassword.html.twig',[
            'form' => $form->createView(),
            'user' => $user
        ]);
    }


    /**
     * Delete a specific user
     * @Route("/utilisateurs/{slug}/supprimer", name="delete_user")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
     * @param User          $user
     * @param ObjectManager $manager
     * @return Response
     */
    public function removeUser(User $user, ObjectManager $manager)
    {
        if($user == $this->getUser()){
            $this->addFlash(
                'warning',
                'Vous ne pouvez pas supprimer votre compte. (Admin)'
            );
            return $this->redirectToRoute('account_show', ['slug'=> $user->getSlug()]);
        }
        if(!empty($user->getProjectmod())){
            foreach($user->getProjectmod() as $project){
                foreach($project->getUsers() as $user){
                    $project->setModerator($user);
                    break;
                }
                if($project->getModerator() == $user){
                    $manager->remove($project);
                } else {
                    $manager->persist($project);
                }
            }
        }
        if(!empty($user->getNotifReceived())){
            foreach($user->getNotifReceived() as $notif){
                $manager->remove($notif->getNotification());
                $manager->remove($notif);
            }
        }
        if(!empty($user->getNotifSent())){
            foreach($user->getNotifSent() as $notif){
                $manager->remove($notif);
            }
        }
        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            'success',
            'L\'utilisateur a bien été supprimé.'
        );

        return $this->redirectToRoute('admin_all_users');
    }

    /**
     * Show all users with his status (active/inactive)
     * @Route("/utilisateurs/activate/{page<\d+>?1}", name="all_users_active")
     * @param integer    $page
     * @param Pagination $pagination
     * @return Response
     */
    public function allUsersActive(int $page, Pagination $pagination)
    {
        $pagination->setEntity(User::class)
                   ->setLimit(60)
                   ->setPage($page);

        return $this->render('admin/users_active.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Edit user's skill
     * @Route("/{slug}/competences", name="edit_skills")
     * @IsGranted("ROLE_FORMER")
     * @param User $user
     * @param Request $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editSkillsUser(User $user, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(EditUserSkillsType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $notif = $user->createSenderNotif(Notification::SKILL, $user->getSlug());
            $user->createUserNotif($notif);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les compétences ont bien été modifiées'
            );

            return $this->redirectToRoute("admin_all_users", ['slug' => 'tout']);
        }
        return $this->render('other/edit_skills.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * Edit the user's datas
     * @Route("/donnees/{slug}/modifier", name="user_data_edit")
     * @Security("is_granted('ROLE_FORMER') or is_granted('ROLE_MEDIATEUR')")
     * @param User          $user
     * @param Request       $request
     * @param ObjectManager $manager
     * @return Response
     */
    public function editUserData(User $user, Request $request, ObjectManager $manager)
    {
        $form = $this->createForm(EditUserDataType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Les données ont bien été modifiées.'
            );
            return $this->redirectToRoute("admin_all_users");
        }
        return $this->render('data/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }

    /**
     * Active/Inactive a specific user
     * @Route("/utilisateur/{slug}/activer", name="user_active")
     * @param User          $user
     * @param ObjectManager $manager
     * @return JsonResponse
     */
    public function editActiveUser(User $user, ObjectManager $manager)
    {
        $user->getIsActive() ? $user->setIsActive(false) : $user->setIsActive(true);
        $manager->persist($user);
        $manager->flush();

        return new JsonResponse();
    }

    /**
     * Remove a specific user's role
     * @Route("/utilisateur/{id}/{id_role}/supprimer", name="role_remove")
     * @Entity("role", expr="repository.find(id_role)")
     * @param User          $user
     * @param Role          $role
     * @param ObjectManager $manager
     * @return Response/JsonResponse
     */
    public function removeUserRole(User $user, Role $role, ObjectManager $manager)
    {
        $role->removeUser($user);
        $manager->persist($role);
        $manager->flush();

        return new JsonResponse();
    }

    /**
     * Add a specific role to a specific user
     * @Route("/utilisateur/{id}/{id_role}/ajouter", name="role_add")
     * @Entity("role", expr="repository.find(id_role)")
     * @param User          $user
     * @param Role          $role
     * @param ObjectManager $manager
     * @return JsonResponse
     */
    public function addUserRole(User $user, Role $role, ObjectManager $manager)
    {
        $role->addUser($user);
        $manager->persist($role);
        $manager->flush();

        return new JsonResponse();
    }

    /**
     * Show all users (all or by promotion) + adding user form
     * @Route("/utilisateurs/{slug}/{page<\d+>?1}", name="all_users")
     * @param string     $slug promo search (all, other, specific promo)
     * @param integer    $page       current page
     * @param Request    $request
     * @param Pagination $pagination pagination service
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function allUsers(string $slug = "tout", int $page, Request $request, Pagination $pagination, UserPasswordEncoderInterface $encoder)
    {
        $manager = $this->getDoctrine()->getManager();
        $prep    = $this->getDoctrine()->getRepository(Promotion::class);

        $promo = $prep->findOneBy(['slug' => $slug]);
        if($slug != "tout" && $slug != "autres" && $promo == null){
            throw new Exception("Route problem");
        }

        $pagination->setEntity(User::class)
                   ->setLimit(14)
                   ->setPage($page);

        if($slug == 'autres'){
            $pagination->setCriteria(['promotion' => null]);
        } else if($slug == 'tout') {
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
}