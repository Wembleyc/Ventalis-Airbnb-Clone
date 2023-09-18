<?php

namespace App\Controller\Admin;

use App\Entity\Messages;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;


class MessagesCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Messages::class;
    }

    public function configureCrud(Crud $crud): Crud
{    dump('Constructeur exécuté');
    return $crud
        ->setEntityLabelInSingular('Utilisateur')
        ->setEntityLabelInPlural('Tous les messages')
        // ...
        ;
}

    
    public function configureFields(string $pageName): iterable
    {    dump('Constructeur exécuté');
        return [
            IdField::new('id'),
            TextEditorField::new('content'),
            DateField::new('timestamp'),

        ];
    }
    
}
