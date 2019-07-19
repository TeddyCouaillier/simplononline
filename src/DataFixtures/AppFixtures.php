<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
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

        for($i = 0; $i < mt_rand(5,10); $i++){
            $user  = new User();

            $user->setFirstName($faker->firstName())
                 ->setLastName($faker->lastName)
                 ->setEmail($faker->email)
                 ->setPassword($this->encoder->encodePassword($user,'test'))
                 ->setTel('06'.mt_rand(1000,9999).mt_rand(1000,9999))
                 ->setZipcode('08000')
                 ->setCity($faker->city)
                 ->setWebsite($faker->url)
                 ->setGithub($faker->url);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
