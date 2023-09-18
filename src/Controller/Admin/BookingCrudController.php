<?php

namespace App\Controller\Admin;

use App\Entity\Booking;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use App\Repository\UserRepository;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use App\Validator\MinimumOneDay;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\TextareaType; // Import the TextareaType
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;

class BookingCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Booking::class;
    }

    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Toutes les réservations');
    }
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->update(Crud::PAGE_INDEX, Action::NEW, function (Action $action) {
                return $action->setLabel('Créer un Booking');
            });
    }

    private function convertRolesToLabel(array $roles): string
    {
        $roleLabels = [
            'ROLE_ADMIN' => 'Admin',
            'ROLE_HOTE' => 'Hôte',
            'ROLE_USER' => 'Utilisateur',
            'ROLE_EMPLOYE' => 'Employé'
        ];

        $convertedRoles = [];
        foreach ($roles as $role) {
            if (isset($roleLabels[$role])) {
                $convertedRoles[] = $roleLabels[$role];
            }
        }

        return implode(', ', $convertedRoles);
    }

    public function configureFields(string $pageName): iterable
    {
        $usersAndHotes = $this->userRepository->findUsersAndHotes();

        // Convertir les entités en un tableau clé-valeur
        $usersChoices = [];
        foreach ($usersAndHotes as $user) {
            $label = $user->getFirstName() . ' ' . $user->getLastName() . ' (' . implode(", ", $user->getRoles()) . ')';
            $usersChoices[$label] = $user->getId(); // Utilisez l'ID de l'utilisateur comme valeur
        }


        return [
            IdField::new('id')->hideOnForm()->setLabel('Booking Id'),
            TextField::new('userFullName', 'Nom/Prénom')->onlyOnIndex(),
            IdField::new('userReservation.id')->setLabel('User Id')
                ->onlyOnIndex(), // Affichez uniquement dans la liste (index)
            DateField::new('start_date')->setLabel("Du"),
            DateField::new('end_date')->setLabel("Au"),
            IntegerField::new('guest_count')->setLabel("Adultes"),
            AssociationField::new('logement')->autocomplete(),
            // AssociationField::new('user')
            //     ->setCrudController(UserCrudController::class)
            //     ->autocomplete(),
            MoneyField::new('priceByNight', 'Prix par nuit')
                ->setVirtual(true)
                ->setCurrency('EUR')
                ->onlyOnIndex(),
            IntegerField::new('numberOfNights', 'Nuits')->onlyOnIndex(),
            MoneyField::new('totalPrice', 'Prix Total')
                ->setVirtual(true) // indique que ce champ est un champ virtuel
                ->setCurrency('EUR')
                ->onlyOnIndex(),

            ChoiceField::new('status_booking')
                ->setLabel('Status')
                ->setChoices([
                    'En attente de confirmation' => Booking::STATUS_PENDING,
                    'Confirmée' => Booking::STATUS_CONFIRMED,
                    'En cours' => Booking::STATUS_ONGOING,
                    'Terminée' => Booking::STATUS_COMPLETED,
                    'Refusée' => Booking::STATUS_DECLINED
                ])

                ->onlyWhenUpdating(), // Affiche dans le formulaire d'édition (mise à jour), pas dans le formulaire de création
            ChoiceField::new('status_booking')
                ->setLabel('Status')
                ->setChoices([
                    'En attente de confirmation' => Booking::STATUS_PENDING,
                    'Confirmée' => Booking::STATUS_CONFIRMED,
                    'En cours' => Booking::STATUS_ONGOING,
                    'Terminée' => Booking::STATUS_COMPLETED,
                    'Refusée' => Booking::STATUS_DECLINED
                ])

                ->onlyOnIndex(), // Affiche dans le formulaire d'édition (mise à jour), pas dans le formulaire de création
            TextEditorField::new('comments_hote', 'Commentaire')
                ->setLabel('Commentaire')
                ->setFormType(TextareaType::class)
                ->onlyWhenUpdating(), // Affiche dans le formulaire d'édition (mise à jour), pas dans le formulaire de création
            TextEditorField::new('comments_hote', 'Commentaire')
                ->setLabel('Commentaire')
                ->setFormType(TextareaType::class)
                ->onlyOnIndex(),


        ];
        // Ajoutez le champ de commentaire conditionnellement


    }


    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(
                ChoiceFilter::new('status_booking', 'Status Booking')
                    ->setChoices([
                        'En attente de confirmation' => Booking::STATUS_PENDING,
                        'Confirmée' => Booking::STATUS_CONFIRMED,
                        'En cours' => Booking::STATUS_ONGOING,
                        'Terminée' => Booking::STATUS_COMPLETED,
                        'Refusée' => Booking::STATUS_DECLINED,
                    ])
            );
    }

    // Ajoutez la méthode persistEntity pour associer automatiquement l'utilisateur connecté à la réservation
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Obtenez l'utilisateur connecté
        $user = $this->getUser();

        // Assurez-vous que l'utilisateur est connecté (non nul)
        if ($user instanceof User) {
            // Associez la réservation à l'utilisateur en utilisant son ID
            $entityInstance->setUserReservation($user);
        }

        // Appelez la méthode persistEntity par défaut pour effectuer la persistance
        parent::persistEntity($entityManager, $entityInstance);
    }
}
