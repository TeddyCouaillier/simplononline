<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
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

        $promos = [];
        for($i = 0 ; $i < mt_rand(4,6) ; $i++){
            $promo = new Promotion();

            $promo->setLabel('Promo 1.'.$i);
            $promo->setNickname($faker->word.' '.$faker->word);
            $promos[$i] = $promo;
            $manager->persist($promo);
        }

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

        $manager->persist($admin);

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

            $manager->persist($user);
        }

        $manager->flush();
    }
}
