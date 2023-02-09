<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Doctrine\Inflector\Rules\Pattern;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class UserControllerTest extends WebTestCase
{
    /**
     * @dataProvider secureUrl
     */
    public function testNotConnected($url): void
    {
        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        $client = static::createClient();

        // Request a specific page
        $crawler = $client->request('GET', $url);
        $client->followRedirects();

        // Validate a successful response and some content
        $this->assertTrue($client->getResponse()->isRedirect('/login'));
    }

    public function secureUrl(): array 
    {
        return [
            'Créer une nouvelle tâche'=>['/tasks/create'],
            'Consulter la liste des tâches à faire'=>['/tasks'],
            'Consulter la liste des tâches terminées'=>['/showTaskFinish'],
        ];
    }

    /**
     * @dataProvider secureUrl
     */
    public function testConnected($url): void
    {
        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        $client = static::createClient();
        $container = static::getContainer();


        // Request a specific page
        $userRepository = $container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('toto@hotmail.com');

        $client->loginUser($testUser);

        $crawler = $client->request('GET', $url);

        // Validate a successful response and some content
        $this->assertResponseIsSuccessful();
    }


}

