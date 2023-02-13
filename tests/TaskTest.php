<?php

namespace App\Tests;

use App\Entity\Task;
use App\Entity\User;
use DateTime;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testTask(): void
    {
        $content = "faire devoirs";
        $createdAt = new DateTime();
        $title = "devoirs maths";
        $user = new User();
        $task= new Task();
        $task->setContent($content);
        $task->setCreatedAt($createdAt);
        $task->setTitle($title);
        $task->setUser($user);
        $this->assertEquals($content, $task->getContent());
        $this->assertEquals($createdAt, $task->getCreatedAt());
        $this->assertEquals($title, $task->getTitle());
        $this->assertEquals($user, $task->getUser());
        $this->assertEmpty($task->getId());
        $this->assertFalse($task->isDone());
        $task->toggle(true);
        $this->assertTrue($task->isDone());

    }
}
