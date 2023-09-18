<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use PharIo\Manifest\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints as Assert;
use EasyCorp\Bundle\EasyAdminBundle\Dto\CrudDto;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use EasyCorp\Bundle\EasyAdminBundle\Orm\EntityRepository;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\FieldDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;


class UserCrudController extends AbstractCrudController
{
    private $passwordHasher;
    private $userRepository;
    private $usersCountPerEmployee = [];


    public function __construct(UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository; // 1. Initialisez la propriété $userRepository
        $this->usersCountPerEmployee = $this->userRepository->countUsersForAllEmployees();
    }

    public function configureCrud(Crud $crud): Crud
    {

        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Gestion des users')
            // ...
        ;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }


    public function configureFields(string $pageName): iterable
    {

        $usersCountPerEmployee = $this->userRepository->countUsersForAllEmployees();

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('email')
                ->setFormTypeOptions([
                    'constraints' => [
                        new Assert\Email([
                            'message' => 'Veuillez entrer un e-mail valide.',
                        ]),
                    ],
                ]),
            TextField::new('firstName', 'First Name'),

            TextField::new('lastName', 'Last Name'),
            TextField::new('telephone'),
            TextField::new('password')
                ->onlyOnForms()
                ->setRequired(false)
                ->setFormTypeOptions([
                    'constraints' => ($pageName === Crud::PAGE_NEW) ? [
                        new Assert\Regex([
                            'pattern' => '/^(?=.*[A-Z])(?=.*\d).{7,}$/',
                            'message' => 'Le mot de passe doit avoir au moins 7 caractères, dont une majuscule et un chiffre.',
                        ]),
                    ] : [],
                ]), // Si vous ne voulez pas que ce champ soit obligatoire lors de la modification
            IdField::new('employee_matricule', 'Employee Matricule')->hideOnForm(),
            AssociationField::new('conseiller')
                ->setLabel('Conseiller')
                ->setFormTypeOptions([
                    'required' => false, // rendre le champ facultatif
                    'placeholder' => 'Aucun conseiller', // texte à afficher quand aucune option n'est sélectionnée
                ])
                ->setQueryBuilder(function (QueryBuilder $qb) {
                    $user = $this->getUser();
                    if ($user instanceof User) {
                        $currentUserId = $user->getId(); // Assurez-vous que la méthode getId existe dans votre classe User
                        return $qb
                            ->andWhere('entity.conseiller = :currentUserId')
                            ->setParameter('currentUserId', $currentUserId);
                    }
                    return $qb; // Vous pourriez retourner une QueryBuilder vide si nécessaire
                }),




            ArrayField::new('rolesWithLabels', 'Rôles')->hideOnForm(), // Utilisez ArrayField pour afficher les rôles avec labels dans la liste
            ChoiceField::new('roles')
                ->setLabel('Rôles')
                ->setChoices([
                    'Administrateur' => 'ROLE_ADMIN',
                    'Utilisateur' => 'ROLE_USER',
                    'Hôte' => 'ROLE_HOTE',
                    'Employé' => 'ROLE_EMPLOYE',
                ])
                ->onlyOnForms()
                ->allowMultipleChoices()
                ->setFormTypeOption('attr', [
                    'onChange' => 'handleRoleChange(event)'
                ]),
            ImageField::new('image_profil')
                ->setBasePath('img/') // Ici, nous spécifions que toutes les images sont affichées à partir du dossier 'img/'
                ->setUploadDir('public/img'), // L'image est stockée dans 'public/img' sur le serveur



        ];
    }
    public function createIndexQueryBuilder(?SearchDto $searchDto = null, ?EntityDto $entityDto = null, ?FieldCollection $fields = null, ?FilterCollection $filters = null): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        /** @var User $currentUser */
        $currentUser = $this->getUser();

        // Si l'utilisateur actuel est un employé (ROLE_EMPLOYE)
        if ($currentUser && in_array('ROLE_EMPLOYE', $currentUser->getRoles())) {
            // Filtrer les utilisateurs dont le conseiller est l'utilisateur actuel
            $queryBuilder->andWhere('entity.conseiller = :currentUserId')
                ->setParameter('currentUserId', $currentUser->getId());
        }

        return $queryBuilder;
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->encodePassword($entityInstance);
        $this->processRoles($entityInstance);
        $this->assignAdvisorWithFewestUsers($entityInstance);

        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if ($entityInstance instanceof User) {
            // Traiter les rôles.
            $this->processRoles($entityInstance);

            // Sauvegardez simplement l'entité mise à jour.
            parent::updateEntity($entityManager, $entityInstance);
        } else {
            parent::updateEntity($entityManager, $entityInstance);
        }
    }


    private function assignAdvisorWithFewestUsers(User $user): void
    {
        // Si l'utilisateur a le rôle 'ROLE_EMPLOYE' ou 'ROLE_ADMIN', ne rien faire.
        if (in_array('ROLE_EMPLOYE', $user->getRoles()) || in_array('ROLE_ADMIN', $user->getRoles())) {
            return;
        }

        // Si un conseiller est déjà attribué manuellement, ne pas écraser cette attribution.
        if ($user->getConseiller() !== null) {
            return;
        }

        // Obtenez tous les employés et le nombre de leurs utilisateurs
        $usersCountPerEmployee = $this->userRepository->countUsersForAllEmployees();
        // Filter ceux qui ont 0 user_count
        $zeroUserAdvisors = array_filter($usersCountPerEmployee, function ($advisor) {
            return $advisor['user_count'] == 0;
        });

        $advisorId = null; // Initialisez la variable

        // Supposons que $zeroUserAdvisors est un tableau d'employés qui ont 0 utilisateurs attribués
        if (!empty($zeroUserAdvisors)) {
            $randomAdvisor = $zeroUserAdvisors[array_rand($zeroUserAdvisors)];
            $advisorId = $randomAdvisor['conseiller_id'];
        } elseif (!empty($usersCountPerEmployee)) {
            $advisorId = $usersCountPerEmployee[0]['conseiller_id'];
        }

        if (!$advisorId) {
            // Si aucun ID de conseiller n'a été trouvé, lancez une exception
            throw new \Exception('Erreur : Aucun ID de conseiller trouvé.');
        }

        // Récupérez l'entité Conseiller (qui est en fait un User avec le rôle ROLE_EMPLOYE) à partir de l'ID
        $advisorEntity = $this->userRepository->findUserById($advisorId);

        if (!$advisorEntity) {
            // Si aucune entité conseiller n'est trouvée avec cet ID, lancez une exception
            throw new \Exception('Erreur : Aucun conseiller trouvé pour l\'ID: ' . $advisorId);
        }

        $user->setConseiller($advisorEntity);
    }


    private function encodePassword($user): void
    {
        if ($user->getPassword()) { // This will check if the password field is not empty.
            $password = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($password);
        }
    }

    private function processRoles($user): void
    {
        $roles = $user->getRoles();
        $user->setRoles($roles);

        // Vérifiez si l'utilisateur est un employé
        if (in_array('ROLE_EMPLOYE', $roles) && null === $user->getEmployeeMatricule()) {
            // Générez un matricule unique
            $matricule = $this->generateUniqueMatricule();
            $user->setEmployeeMatricule($matricule);
        }
    }

    private function generateUniqueMatricule(): int
    {
        $attempts = 0;
        do {
            $matricule = rand(1000, 9999);
            $existingUser = $this->userRepository->findOneBy(['employee_matricule' => $matricule]);
            $attempts++;
        } while ($existingUser && $attempts < 30);

        if ($existingUser) {
            // Générer une exception si après 10 tentatives, nous ne pouvons pas trouver un matricule unique.
            throw new \Exception('Could not generate a unique matricule.');
        }

        return $matricule;
    }
}
