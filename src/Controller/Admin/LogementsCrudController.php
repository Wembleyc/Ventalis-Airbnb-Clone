<?php

namespace App\Controller\Admin;

use App\Entity\Logements;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection;


class LogementsCrudController extends AbstractCrudController
{    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    

    public static function getEntityFqcn(): string
    {
        return Logements::class;
    }

    public function configureFields(string $pageName): iterable
    {        dump('Constructeur exécuté');

        return [
            IdField::new('id')->hideOnForm()->setLabel('Id_logement'),
            TextField::new('title')->setLabel('Titre'),
            TextEditorField::new('description'),
            MoneyField::new('price_by_night')->setCurrency('EUR')->setLabel('Prix par nuit'),
            BooleanField::new('is_valid')->setLabel('Online/Offline'),
            IntegerField::new('numero'),
            TextField::new('street')->setLabel('Rue'),
            IntegerField::new('zip_code')->setLabel('Code Postale')
            ,
            ImageField::new('image')
                ->setBasePath('img/') // Ici, nous spécifions que toutes les images sont affichées à partir du dossier 'img/'
                ->setUploadDir('public/img'), // L'image est stockée dans 'public/img' sur le serveur
            ChoiceField::new('status')
                ->setLabel('Statut')
                ->setChoices([
                    'Attente de validation' => Logements::STATUS_PENDING,
                    'Validé' => Logements::STATUS_APPROVED,
                    'Refusé' => Logements::STATUS_REJECTED,
                ])
                ->hideOnForm(),
                IdField::new('hote')->setLabel('Hôte'),
                



        ];
    } 
    public function createEntity(string $entityFqcn)
    {           dump('Constructeur exécuté');

        $logement = new Logements();
        $logement->setStatus(Logements::STATUS_PENDING);
    
        $currentUser = $this->security->getUser();
        dump($currentUser);
        // Dump l'utilisateur pour voir s'il est correctement récupéré
        // dump($currentUser);
    
        if ($currentUser instanceof User && in_array('ROLE_HOTE', $currentUser->getRoles())) {
            $logement->setHote($currentUser);
        }
    
        return $logement;
    }
    // public function createIndexQueryBuilder(
    //     SearchDto $searchDto,
    //     EntityDto $entityDto,
    //     FieldCollection $fields,
    //     FilterCollection $filters
    // ): QueryBuilder {
    //     $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
    //     $currentUser = $this->security->getUser();
        
    //     // Si l'utilisateur est connecté et possède le rôle 'ROLE_HOTE', ajuster la requête
    //     if ($currentUser instanceof User && in_array('ROLE_HOTE', $currentUser->getRoles())) {
    //         $queryBuilder
    //             ->andWhere($queryBuilder->expr()->eq('entity.hote', ':currentHote'))
    //             ->setParameter('currentHote', $currentUser);
    //     }
        
    //     return $queryBuilder;
    // }
    

    
    
}