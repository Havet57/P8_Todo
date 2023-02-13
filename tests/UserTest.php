<?php

namespace App\Tests;

use App\Entity\Task;
use app\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testUser(): void
    {
        $email = "tonton@hotmail.com";
        $password = "tonton12345";
        $username = "tonton";
        $task = new Task();
        $user = new User();
        $user->setEmail($email);
        $user->setPassword($password);
        $user->setUsername($username);
        $user->setRoles([]);
        $this->assertEquals($email, $user->getEmail());
        $this->assertEquals($password, $user->getPassword());
        $this->assertEquals($username, $user->getUsername());
        $this->assertArrayHasKey('ROLE_USER', $user->getRoles());
        $this->assertEmpty($user->getSalt());
        $this->assertEquals($email, $user->getUserIdentifier());
        $this->assertEmpty($user->getTasks());
        $this->assertEmpty($user->getId());
        $user->addTask($task);
        $this->assertEquals([$task], $user->getTasks());
        $user->removeTask($task);
        $this->assertEmpty($user->getTasks());


    }
}
