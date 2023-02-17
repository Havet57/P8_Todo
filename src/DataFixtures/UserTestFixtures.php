<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;


class UserTestFixtures extends Fixture implements FixtureGroupInterface

{
    private UserPasswordHasherInterface $hasher;

    public static function getGroups(): array
    {
        return ['test'];
    }


    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $usersData=[
            ['id'=>1, 'username'=>'toto', 'roles'=>['ROLE_USER'], 'email'=>'toto@hotmail.com', 'password'=>'toto12345'],
            ['id'=>2, 'username'=>'lili', 'roles'=>['ROLE_ADMIN'], 'email'=>'lili@hotmail.com', 'password'=>'lili12345'],
            
        ];


        foreach($usersData as $userData){
            $user = new User();
            $user->setUsername($userData['username']);
            $user->setRoles($userData['roles']);
            $user->setEmail($userData['email']);
            $password = $this->hasher->hashPassword($user, $userData['password']);
            $user->setPassword($password);
            $this->setReference('user-'.$userData['id'], $user);

            $manager->persist($user);
        }

        

        $manager->flush();
    }
}
