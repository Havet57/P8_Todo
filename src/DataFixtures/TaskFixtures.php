<?php

namespace App\DataFixtures;


use App\Entity\Task;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;


class TaskFixtures extends Fixture implements DependentFixtureInterface
{

    public function getDependencies()
    {
        return [
            UserFixtures::class,
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

