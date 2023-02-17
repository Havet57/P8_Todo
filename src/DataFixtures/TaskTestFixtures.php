<?php

namespace App\DataFixtures;


use App\Entity\Task;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;



class TaskTestFixtures extends Fixture implements DependentFixtureInterface, FixtureGroupInterface

{

    public static function getGroups(): array
    {
        return ['test'];
    }

    public function getDependencies()
    {
        return [
            UserTestFixtures::class,
        ];
    }

    public function load(ObjectManager $manager): void
    {
        $tasksData=[
            ['id'=>1, 'user-id'=>2, 'done'=>true, 'title'=>'Faire les courses',  'content'=>'Carottes, poulet, saumon, croquettes etc'],
            ['id'=>2, 'user-id'=>1,'done'=>false,'title'=>'devoir de philo',  'content'=>'Sujet: tous les moyens sont-ils vraiment bon pour arriver à ses fins ?'],
        ];
        foreach($tasksData as $taskData) {
            $task = new Task();
            $task->setTitle($taskData['title']);
            $task->setContent($taskData['content']);
            $task->setUser($this->getReference('user-'.$taskData['user-id']));
            $task->toggle($taskData['done']);
            $task->setCreatedAt(new DateTime());
            

            $manager->persist($task);
        }
        $manager->flush();
    }
}

