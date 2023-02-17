<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Repository\UserRepository;
use App\Repository\TaskRepository;



class TaskControllerTest extends WebTestCase
{
    public function testSomething(): void
    {
        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        $client = static::createClient();

        // Request a specific page
        $crawler = $client->request('GET', '/');

        // Validate a successful response and some content
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', "Bienvenue sur Todo List, l'application vous permettant de gérer l'ensemble de vos tâches sans effort !");
        $this->assertSelectorExists('.btn-success');
    }

    public function testCreateAction()
    {
    // Crée une instance du client HTTP de Symfony pour faire des requêtes à l'application
    $client = static::createClient();
    $container = static::getContainer();

    //Se connecte
    $userRepository = $container->get(UserRepository::class);
    $testUser = $userRepository->findOneByEmail('lili@hotmail.com');

    $client->loginUser($testUser);

    // Envoie une requête GET à la route task_create
    $crawler = $client->request('GET', '/tasks/create');

    // Vérifie que la réponse a un code HTTP 200 (OK)
    $this->assertEquals(200, $client->getResponse()->getStatusCode());

    // Remplir le formulaire pour créer une tâche
    $form = $crawler->selectButton('Ajouter')->form([
        'task[title]' => 'Tâche de test',
        'task[content]' => 'Contenu de test'
    ]);
    $client->submit($form);

    // Vérifie que la réponse a un code HTTP 302 (Redirection)
    $this->assertEquals(302, $client->getResponse()->getStatusCode());

    // Vérifie que la redirection se fait vers la route task_list
    $this->assertEquals('/tasks', $client->getResponse()->headers->get('Location'));

    // Vérifie que le message de succès est présent dans la session
    $crawler = $client->followRedirect();
    $this->assertStringContainsString('La tâche a été bien été ajoutée.', $client->getResponse()->getContent());
    }


    public function testListAction()
    {
        // Crée une instance du client HTTP de Symfony pour faire des requêtes à l'application
        $client = static::createClient();
        $container = static::getContainer();

        // Se connecter
        $userRepository = $container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('lili@hotmail.com');

        $client->loginUser($testUser);

        // Envoie une requête GET à la route task_list
        $client->request('GET', '/tasks');
        

        // Vérifie que la réponse a un code HTTP 200 (OK)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        // Vérifie que la réponse contient le texte "Liste des tâches"
        $this->assertStringContainsString('Liste des tâches à faire', $client->getResponse()->getContent());
    }

    public function testEditAction()
    {
        // Crée une instance du client HTTP de Symfony pour faire des requêtes à l'application
        $client = static::createClient();
        $container = static::getContainer();
    
        $userRepository = $container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('lili@hotmail.com');
    
        $client->loginUser($testUser);
    
        // Trouve une tâche en base de données pour éditer
        $taskRepository = $container->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['user' => $testUser]);
    
        // Envoie une requête GET à la route task_edit pour récupérer le formulaire d'édition
        $client->request('GET', '/tasks/' . $task->getId() . '/edit');
    
        // Vérifie que la réponse a un code HTTP 200 (OK)
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    
        // Remplit le formulaire avec de nouvelles données
        $form = $client->getCrawler()->filter('form[name=task]')->form([
            'task[title]' => 'Nouveau titre',
            'task[content]' => 'Nouveau contenu',
        ]);
    
        // Envoie une requête POST pour soumettre le formulaire d'édition
        $client->submit($form);
    
        // Vérifie que la réponse redirige vers la page de la liste des tâches
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
        $this->assertEquals('/tasks', $client->getResponse()->headers->get('location'));
    
        // Vérifie que la tâche a été modifiée en base de données
        $updatedTask = $taskRepository->findOneBy(['id' => $task->getId()]);
        $this->assertEquals('Nouveau titre', $updatedTask->getTitle());
        $this->assertEquals('Nouveau contenu', $updatedTask->getContent());
    }

    
    public function testToggleTaskAction()
    {
        // Crée une instance du client HTTP de Symfony pour faire des requêtes à l'application
        $client = static::createClient();
        $container = static::getContainer();
    
        $userRepository = $container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('lili@hotmail.com');
    
        $client->loginUser($testUser);
    
        // Trouve une tâche existante
        $taskRepository = $container->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['user' => $testUser]);
        $taskId = $task->getId();
    
        // Envoie une requête GET à la route task_toggle pour basculer l'état de la tâche
        $client->request('GET', sprintf('/tasks/%d/toggle', $taskId));
    
        // Vérifie que la réponse a un code HTTP 302 (Redirection)
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    
        // Vérifie que la tâche a bien été marquée comme terminée ou non terminée selon son état initial
        $this->assertFalse($task->isDone());
        $client->request('GET', sprintf('/tasks/%d/toggle', $taskId));
        $task = $taskRepository->find($taskId);
        $this->assertTrue($task->isDone());
    }

    public function testDeleteTaskAction()
    {
        // Crée une instance du client HTTP de Symfony pour faire des requêtes à l'application
        $client = static::createClient();
        $container = static::getContainer();
    
        $userRepository = $container->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('lili@hotmail.com');
    
        $client->loginUser($testUser);
    
        // Trouve une tâche existante
        $taskRepository = $container->get(TaskRepository::class);
        $task = $taskRepository->findOneBy(['user' => $testUser]);
        $taskId = $task->getId();
        // Envoie une requête GET à la route task_toggle pour basculer l'état de la tâche
        $client->request('GET', sprintf('/tasks/%d/delete', $taskId));

        $task = $taskRepository->find($taskId);
        $this->assertEmpty($task);
    }
    
}

