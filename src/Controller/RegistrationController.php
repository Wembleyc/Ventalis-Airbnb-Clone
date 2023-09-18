<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class RegistrationController extends AbstractController
{
    /**
     * Handles user registration and assigns the user to an employee with a given matricule.
     *
     * @Route("/register/{employeeMatricule}", name="app_register")
     * @param Request $request
     * @param string|null $employeeMatricule
     * @param UserPasswordHasherInterface $userPasswordHasher
     * @param EntityManagerInterface $entityManager
     * @param UserRepository $userRepository
     * @return Response
     */
    public function register(Request $request, ?int $advisorId, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserRepository $userRepository): Response
{
    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    // Récupérez tous les IDs des employés ayant le rôle ROLE_EMPLOYE
    $allEmployeeIds = $userRepository->findAllEmployeeIdsByRole();

    if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si le formulaire est soumis et valide

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()
                )
            );
            $user->setRoles(['ROLE_USER']);

            // Vérifiez si l'utilisateur a un conseiller attribué
            if (!$advisorId) {
                // Choisissez un ID d'employé aléatoirement
                $randomAdvisorId = $allEmployeeIds[array_rand($allEmployeeIds)];
    
                // Lorsque vous choisissez un conseiller aléatoire :
                $randomAdvisor = $userRepository->find($randomAdvisorId);
    
                // Vérifiez si le conseiller a été trouvé
                if (!$randomAdvisor) {
                    throw new \Exception('Error: Advisor with the given ID not found.');
                }
    
                // Attribuez le conseiller à l'utilisateur
                $user->setConseiller($randomAdvisor);
            } else {
                // Vérifiez si le conseiller avec cet ID existe et a le rôle ROLE_EMPLOYE
                $advisor = $userRepository->find($advisorId);
    
                if (!$advisor || !in_array('ROLE_EMPLOYE', $advisor->getRoles())) {
                    throw new AccessDeniedException('Invalid advisor ID provided.');
                }
    
                // Attribuez le conseiller à l'utilisateur
                $user->setConseiller($advisor);
            }

            // Persist the user entity
            $entityManager->persist($user);
            $entityManager->flush();


            // dump('New User Persisted: ', $user);


            // Vérifiez si l'utilisateur est persisté
            $this->addFlash('notice', 'Un e-mail a été envoyé pour validation. Veuillez vérifier votre boîte de réception. User has been successfully registered and assigned to an advisor with matricule: ' . $user->getConseiller()->getEmployeeMatricule());

            return $this->redirectToRoute('app_register');
        }

        // Vérifiez si le formulaire n'est pas soumis ou n'est pas valide

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}