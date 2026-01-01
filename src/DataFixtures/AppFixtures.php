<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class AppFixtures extends Fixture
{
    private $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);
        $user = new User();
        $user->setUsername('Admin');
        $user->setEmail('Admin@gmail.com');
        //$user->setCreatedAt(new \DateTime());
        $user->setRoles(['ROLE_Admin']);
        $user->setAdressephysique('No 1234, Av. des Admins');
        $password = $this->userPasswordHasher->hashPassword($user, "Admin@123");
        $user->setPassword($password);
//        $user->setPassword(
//            $userPasswordHasher->hashPassword(
//                $user, "Admin@123")
//        );
        $manager->persist($user);
        $manager->flush();
    }
}
