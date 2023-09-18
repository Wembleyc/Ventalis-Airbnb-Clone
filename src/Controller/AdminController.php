<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @Route("/admin")
 */
class AdminController extends AbstractController
{
    private ManagerRegistry $managerRegistry;

    public function __construct(ManagerRegistry $managerRegistry)
    {
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @Route("/create-user", name="admin_create_user")
     */
    public function createUser(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        try {
            $user = new User();
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $role = $form->get('role')->getData();
                switch ($role) {
                    case 'admin':
                        if ($this->isGranted('ROLE_ADMIN')) {
                            return $this->redirectToRoute('create_user');
                        }
                        $user->setRoles(['ROLE_ADMIN']);
                        break;
                    case 'employee':
                        if (!$this->isGranted('ROLE_ADMIN')) {
                            return $this->redirectToRoute('app_login');
                        }
                        $user->setRoles(['ROLE_EMPLOYEE']);
                        break;
                    case 'host':
                        if (!$this->isGranted('ROLE_USER')) {
                            return $this->redirectToRoute('app_login');
                        }
                        $user->setRoles(['ROLE_HOST']);
                        break;
                    default:
                        if (!$this->isGranted('ROLE_USER')) {
                            return $this->redirectToRoute('app_login');
                        }
                        $user->setRoles(['ROLE_USER']);
                        break;
                }

                $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));

                $entityManager = $this->managerRegistry->getManager();
                $entityManager->persist($user);
                $entityManager->flush();

                // Redirect the user to the create_user.html.twig page
                return $this->redirectToRoute('admin_create_user');
            }
        } catch (\Exception $e) {
            // Handle the exception here
            return new Response('Something went wrong.');
        }

        return $this->render('admin/create_user.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/create-initial-admin", name="create_initial_admin")
     */
    public function createInitialAdmin(UserPasswordHasherInterface $passwordHasher): Response
    {
        $admin = new User();
        $admin->setFirstName('John');
        $admin->setLastName('Doe');
        $admin->setEmail('admin@example.com');
        $admin->setTelephone('123-456-7890');
        $admin->setPassword($passwordHasher->hashPassword($admin, '123456'));
        $admin->setRoles(['ROLE_ADMIN']);

        $entityManager = $this->managerRegistry->getManager();
        $entityManager->persist($admin);
        $entityManager->flush();

        return new Response('Initial admin created.');
    }
    

}
