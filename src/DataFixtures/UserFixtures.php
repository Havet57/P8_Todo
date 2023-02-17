<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $usersData=[
            ['id'=>1, 'username'=>'kevin', 'roles'=>['ROLE_USER'], 'email'=>'kevin@hotmail.com', 'password'=>'kevin12345'],
            ['id'=>2, 'username'=>'julie', 'roles'=>['ROLE_ADMIN'], 'email'=>'julie@hotmail.com', 'password'=>'julie12345'],
            
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
