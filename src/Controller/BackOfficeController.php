<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Logements;
use App\Entity\Messages;
use App\Entity\Review;
use App\Entity\User;
use App\Form\UpdateUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Form\CreateUserType;



class BackOfficeController extends AbstractController
{
    #[Route('/back/office', name: 'app_back_office')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            throw $this->createAccessDeniedException();
        }

        $bookingRepository = $entityManager->getRepository(Booking::class);
        $logementsRepository = $entityManager->getRepository(Logements::class);
        $messagesRepository = $entityManager->getRepository(Messages::class);
        $reviewRepository = $entityManager->getRepository(Review::class);
        $userRepository = $entityManager->getRepository(User::class);

        $bookings = $bookingRepository->findAll();
        $logements = $logementsRepository->findAll();
        $messages = $messagesRepository->findAll();
        $reviews = $reviewRepository->findAll();
        $user = $userRepository->findAll();

        $roleLabels = [
            'ROLE_ADMIN' => 'Administrateur',
            'ROLE_USER' => 'Utilisateur',
            'ROLE_HOTE' => 'Hôte',
            'ROLE_EMPLOYE' => 'Employé',
        ];

        return $this->render('back_office/index.html.twig', [
            'bookings' => $bookings,
            'logements' => $logements,
            'messages' => $messages,
            'reviews' => $reviews,
            'users' => $user,
            'roleLabels' => $roleLabels, // Passer les labels de rôles à la vue

        ]);




    }
    #[Route('/back/office/user/{id}', name: 'app_back_office_user_update', methods: ['GET', 'POST'])]
    public function updateUser(Request $request, int $id, EntityManagerInterface $entityManager)
    {
        // Récupérer l'utilisateur spécifique par son identifiant ($id)
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }

        $form = $this->createForm(UpdateUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Avant d'enregistrer l'entité User
            $rolesArray = $form->get('roles')->getData();
            // Assurez-vous que chaque utilisateur a au moins le rôle ROLE_USER
            if (!in_array('ROLE_USER', $rolesArray)) {
                $rolesArray[] = 'ROLE_USER';
            }
            $user->setRoles($rolesArray);

            $entityManager->flush();

            return $this->redirectToRoute('app_back_office');
        }

        return $this->render('back_office/user_update.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }
    #[Route('/create/admin', name: 'app_create_admin')]
    public function createAdmin(EntityManagerInterface $entityManager)
    {
        // Créer un nouvel utilisateur avec le rôle administrateur (ROLE_ADMIN)
        $admin = new User();
        $admin->setLastName('Admin'); // Remplacez par le nom souhaité pour l'administrateur
        $admin->setFirstName('Super'); // Remplacez par le prénom souhaité pour l'administrateur
        $admin->setEmail('superadmin@ventalis.com'); // Remplacez par l'e-mail souhaité pour l'administrateur
        $admin->setTelephone('0123456789'); // Remplacez par le numéro de téléphone souhaité pour l'administrateur

        // Hachez le mot de passe avant de l'enregistrer dans la base de données
        $password = '123456789';
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $admin->setPassword($hashedPassword);

        // Définir le rôle administrateur
        $admin->setRoles(['ROLE_ADMIN']); // Remplacez si nécessaire par le nom du champ de rôle dans votre entité User

        // Enregistrez l'utilisateur administrateur dans la base de données
        $entityManager->persist($admin);
        $entityManager->flush();

        return new Response('Compte administrateur créé avec succès !');
    }
    #[Route('/back/office/create_user', name: 'app_back_office_create_user', methods: ['GET', 'POST'])]
public function createUser(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
{
    // Créer un nouvel utilisateur
    $user = new User();

    $form = $this->createForm(CreateUserType::class, $user);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Hacher le mot de passe avant de l'enregistrer dans la base de données
        $plainPassword = $form->get('plainPassword')->getData();
        $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        // Vérifier si l'utilisateur est un employé
        if (in_array('ROLE_EMPLOYE', $user->getRoles())) {
            // Générer un numéro de matricule unique pour cet employé
            // Ici, je vais juste utiliser un nombre aléatoire comme exemple
            // Assurez-vous de remplacer ce code par une logique qui convient à vos besoins
            $matricule = rand(1000, 9999);
            $user->setEmployeeMatricule($matricule);
        }

        // Enregistrez l'utilisateur dans la base de données
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('app_back_office');
    }

    return $this->render('back_office/create_user.html.twig', [
        'form' => $form->createView(),
    ]);
}

    /**
     * @Route("/back-office/user/delete/{id}", name="app_back_office_user_delete")
     */
    public function deleteUser($id, EntityManagerInterface $em)
    {
        $user = $em->getRepository(User::class)->find($id);
        if (!$user) {
            throw $this->createNotFoundException('Aucun utilisateur avec l\'id ' . $id . ' trouvé.');
        }

        $em->remove($user);
        $em->flush();

        return $this->redirectToRoute('app_back_office'); // Redirige vers la page principale de la gestion des utilisateurs
    }
    /**
     * @Route("/back-office/booking/delete/{id}", name="app_back_office_booking_delete")
     */
    public function deleteBooking($id, EntityManagerInterface $em)
    {
        $booking = $em->getRepository(Booking::class)->find($id);
        if (!$booking) {
            throw $this->createNotFoundException('Aucune réservation avec l\'id ' . $id . ' trouvée.');
        }

        $em->remove($booking);
        $em->flush();

        return $this->redirectToRoute('app_back_office'); // Redirige vers la page principale de la gestion
    }

    /**
     * @Route("/back-office/message/delete/{id}", name="app_back_office_message_delete")
     */
    public function deleteMessage($id, EntityManagerInterface $em)
    {
        $message = $em->getRepository(Messages::class)->find($id);
        if (!$message) {
            throw $this->createNotFoundException('Aucun message avec l\'id ' . $id . ' trouvé.');
        }

        $em->remove($message);
        $em->flush();

        return $this->redirectToRoute('app_back_office'); // Redirige vers la page principale de la gestion
    }

    /**
     * @Route("/back-office/review/delete/{id}", name="app_back_office_review_delete")
     */
    public function deleteReview($id, EntityManagerInterface $em)
    {
        $review = $em->getRepository(Review::class)->find($id);
        if (!$review) {
            throw $this->createNotFoundException('Aucun avis avec l\'id ' . $id . ' trouvé.');
        }

        $em->remove($review);
        $em->flush();

        return $this->redirectToRoute('app_back_office'); // Redirige vers la page principale de la gestion
    }

    /**
     * @Route("/back-office/logement/delete/{id}", name="app_back_office_logement_delete")
     */
    public function deleteLogement($id, EntityManagerInterface $em)
    {
        $logement = $em->getRepository(Logements::class)->find($id);
        if (!$logement) {
            throw $this->createNotFoundException('Aucun logement avec l\'id ' . $id . ' trouvé.');
        }

        $em->remove($logement);
        $em->flush();

        return $this->redirectToRoute('app_back_office'); // Redirige vers la page principale de la gestion
    }


}