<?php

namespace App\Tests;

use App\Repository\UserRepository;
use Doctrine\Inflector\Rules\Pattern;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\PasswordHasherInterface;

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
        $form['user[username]'] = 'user';
        $form['user[password][first]'] = 'user12345';
        $form['user[password][second]'] = 'user12345';
        $form['user[email]'] = 'user@hotmail.com';
        $client->submit($form);
        $client->followRedirects();
        $this->assertTrue($client->getResponse()->isRedirect('/users'));
    }


    public function testEditAction()
    {
        $client = static::createClient();
        $container = static::getContainer();

        // Récupération d'un utilisateur existant
        $userRepository = $container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('lili@hotmail.com');
        $user = $userRepository->findOneByEmail('user@hotmail.com');
        $client->loginUser($testUser);
        

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
        $user = $userRepository->findOneByEmail('new_email@example.com');
        $this->assertEquals('new_username', $user->getUsername());
        $this->assertEquals('new_email@example.com', $user->getEmail());
    }

        /**
 * Test the path /users/{id}/delete
 *
 * @return void
 */
public function testDeleteAction(): void
{
    $client = static::createClient();
    $container = static::getContainer();

    // Login as a user with enough privileges
    $userRepository = $container->get(UserRepository::class);
    $testUser = $userRepository->findOneByEmail('lili@hotmail.com');

    $client->loginUser($testUser);

    // Find the user to delete
    $userToDelete = $userRepository->findOneByUsername('new_username');

    // Send a DELETE request to the delete action
    $client->request('DELETE', '/users/' . $userToDelete->getId() . '/delete');

    // Assert that the response is a redirect to the user list page
    $client->followRedirects();
    $this->assertTrue($client->getResponse()->isRedirect('/users'));

    // Assert that the user has been deleted from the database
    $deletedUser = $userRepository->findOneByUsername('test');
    $this->assertNull($deletedUser);
}

    

}

