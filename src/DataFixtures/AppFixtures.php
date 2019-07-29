<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Data;
use App\Entity\Role;
use App\Entity\User;
use App\Entity\Skills;
use App\Entity\Language;
use App\Entity\Promotion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('FR-fr');

        // Language section
        $languages = [
            "HTML/CSS","Javascript","PHP","SQL","Symfony","Angular","Logiciel","Divers"
        ];
        foreach($languages as $label){
            $language = new Language();
            $language->setLabel($label);
            $manager->persist($language);
        }

        // Data section
        $datas = [
            "Adresse mail personnelle",
            "Adresse mail mot de passe",
            "FTP lien link",
            "FTP personnel",
            "FTP username",
            "FTP mot de passe",
            "SFTP port",
            "BDD adresse",
            "BDD host conf.inc.php",
            "BDD username",
            "BDD mot de passe",
            "BDD port"
        ];
        $tabDatas = [];
        $index = 0;
        foreach($datas as $label){
            $data = new Data();
            $data->setLabel($label);
            $tabDatas[$index++] = $data;
            $manager->persist($data);
        }

        // Skills section
        $skills = [
            "Maquetter une application" => "test",
            "Réaliser une interface utilisateur web statique et adaptable" => "test",
            "Développer une interface utilisateur web dynamique" => "test",
            "Réaliser une interface utilisateur avec une solution de gestion de contenu ou e-commerce" => "test",
            "Créer une base de données" => "test",
            "Développer les composants d'accès aux données" => "test",
            "Développer la partie back-end d'une application web ou web mobile" => "test",
            "Mettre en oeuvre des composants dans une application de gestion de contenu ou e-commerce" => "test"
        ];
        $tabSkills = [];
        $index = 0;
        foreach($skills as $label => $description){
            $skill = new Skills();
            $skill->setLabel($label)
                  ->setDescription($description);
            $tabSkills[$index++] = $skill;
            $manager->persist($skill);
        }

        // User role section
        $adminRole = new Role();
        $adminRole->setTitle(User::ADMIN);
        $manager->persist($adminRole);
        $adminMediateur = new Role();
        $adminMediateur->setTitle(User::MEDIATEUR);
        $manager->persist($adminMediateur);

        // Promo section
        $promos = [];
        for($i = 0 ; $i < mt_rand(4,6) ; $i++){
            $promo = new Promotion();

            $promo->setLabel('Promo 1.'.$i);
            $promo->setNickname($faker->word.' '.$faker->word);
            $promo->setCurrent(false);
            $promos[$i] = $promo;
            $manager->persist($promo);
        }

        // Super admin
        $admin = new User();
        $admin->setFirstName('Teddy')
                 ->setLastName('Couaillier')
                 ->setEmail('teddy.couaillier@gmail.com')
                 ->setPassword($this->encoder->encodePassword($admin,'test'))
                 ->setTel('0663435810')
                 ->setZipcode('08200')
                 ->setCity('Saint-Menges')
                 ->setWebsite($faker->url)
                 ->setGithub($faker->url)
                 ->setAvatar('CouaillierTeddy17.jpeg')
                 ->setPromotion($promos[1]);
        $admin->addUserRole($adminRole);
        $admin->initializeDatas($tabDatas);
        $admin->initializeSkills($tabSkills);

        $manager->persist($admin);

        // User section
        for($i = 0; $i < mt_rand(25,35); $i++){
            $user  = new User();

            $user->setFirstName($faker->firstName())
                 ->setLastName($faker->lastName)
                 ->setEmail($faker->email)
                 ->setPassword($this->encoder->encodePassword($user,'test'))
                 ->setTel('06'.mt_rand(1000,9999).mt_rand(1000,9999))
                 ->setZipcode('08000')
                 ->setCity($faker->city)
                 ->setWebsite($faker->url)
                 ->setGithub($faker->url)
                 ->setAvatar('avatar.png')
                 ->setPromotion($promos[mt_rand(0,sizeof($promos)-1)]);
            $user->initializeDatas($tabDatas);
            $user->initializeSkills($tabSkills);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
