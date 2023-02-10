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

    public function adminUrl(): array 
    {
        return [
            'consulter la liste des utilisateurs'=>['/users'],
            'créer un nouvel utilisateur'=>['/users/create'],
            'Modifier utilisateur (ici toto)'=>['/users/1/edit'],
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

        /**
     * @dataProvider adminUrl
     */
    public function testAdmin($url): void
    {
        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        $client = static::createClient();
        $container = static::getContainer();


        // Request a specific page
        $userRepository = $container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('lili@hotmail.com');

        $client->loginUser($testUser);

        $crawler = $client->request('GET', $url);

        // Validate a successful response and some content
        $this->assertResponseIsSuccessful();
    }

        /**
     * test path /create with notlog
     *
     * @return void
     */
    public function testCreateAction(): void
    {

        $client = static::createClient();
        $container = static::getContainer();


        // Request a specific page
        $userRepository = $container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('lili@hotmail.com');

        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/users/create');

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'test';
        $form['user[password][first]'] = 'test12345';
        $form['user[password][second]'] = 'test12345';
        $form['user[email]'] = 'test@hotmail.com';
        $client->submit($form);
        $client->followRedirects();
        $this->assertTrue($client->getResponse()->isRedirect('/users'));
    }


    public function testEditAction()
    {
        $client = static::createClient();
        $container = static::getContainer();
        $passwordHasher = $container->get(PasswordHasher::class);

        // Récupération d'un utilisateur existant
        $userRepository = $container->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('toto@hotmail.com');

        $crawler = $client->request('GET', '/users/'.$user->getId().'/edit');

        $form = $crawler->selectButton('Modifier')->form();

        // Remplissage des champs du formulaire avec des données valides
        $form['user[username]'] = 'new_username';
        $form['user[email]'] = 'new_email@example.com';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';

        $client->submit($form);

        // Vérification de la réponse
        $this->assertResponseRedirects('/users');

        // Vérification de la modification de l'utilisateur
        $user = $userRepository->findOneByEmail('toto@hotmail.com');
        $this->assertEquals('new_username', $user->getUsername());
        $this->assertEquals('new_email@example.com', $user->getEmail());
        $this->assertTrue($passwordHasher->checkPassword($user, 'password'));
    }


}

