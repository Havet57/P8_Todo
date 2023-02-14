<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Form\FormError;


/**
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{

    private EntityManagerInterface $em;
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher)
    {
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * @Route("/users", name="user_list")
     */
    public function listAction()
    {
        return $this->render('user/list.html.twig', ['users' => $this->em->getRepository(User::class)->findAll()]);
    }

    /**
     * @Route("/users/create", name="user_create")
     */
    public function createAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $form->get('password')->getData();
            dump($password);

            if (!empty($password)) {
                $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
                $user->setPassword($hashedPassword);

                $this->em->persist($user);
                $this->em->flush();
    
                $this->addFlash('success', "L'utilisateur a bien été ajouté.");
    
                return $this->redirectToRoute('user_list');
            } else {
               $form->addError(new FormError("Le mot de pass est vide"));
            }

           
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     */
    public function editAction(User $user, Request $request)
{
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $password = $form->get('password')->getData();


        if (!empty($password)) {
            $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
            $user->setPassword($hashedPassword);
        } else {
            $user->setPassword($user->getPassword());
        }

        $this->em->flush();

        $this->addFlash('success', "L'utilisateur a bien été modifié");

        return $this->redirectToRoute('user_list');
    }

    return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
}

/**
*@Route("/users/{id}/delete", name="user_delete")
*/
public function deleteAction(User $user)
{
$this->em->remove($user);
$this->em->flush();

$this->addFlash('success', "L'utilisateur a bien été supprimé.");

return $this->redirectToRoute('user_list');
}
}
