<?php

namespace App\Controller\Admin;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Filter\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Booking;
use App\Entity\Logements;
use App\Entity\Messages;
use App\Entity\User; // Assurez-vous d'avoir ce use si vous allez référencer le CRUD des utilisateurs
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Proxies\__CG__\App\Entity\User as EntityUser;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use App\Controller\Admin\UserCrudController; // Assurez-vous d'inclure le contrôleur CRUD de l'utilisateur
use App\Controller\Admin\MessagesCrudController; // Si vous ne l'avez pas déjà fait, incluez le contrôleur CRUD des messages

class DashboardController extends AbstractDashboardController
{
    private $authorizationChecker;
    private $userCrudController; // Ajoutez cette ligne

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, UserCrudController $userCrudController) // Modifiez cette ligne
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->userCrudController = $userCrudController;

    }

    public function configureAssets(): Assets
    {
        return Assets::new()->addCssFile('public/css/custom-easyadmin.css');
    }
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {   dump("Test");
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
    
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
        } elseif ($this->authorizationChecker->isGranted('ROLE_HOTE')) {
            return $this->redirect($adminUrlGenerator->setController(LogementsCrudController::class)->generateUrl());
        } elseif ($this->authorizationChecker->isGranted('ROLE_EMPLOYE')) {
            // Ajoutez la redirection vers UserCrudController pour les ROLE_EMPLOYE 
            return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());
        }
    
     
        return $this->redirect('/');
    }
    

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()->setTitle('Ventalis');
    }

    public function configureMenuItems(): iterable
    {
        $user = $this->getUser();
    
  

        // Pour ROLE_ADMIN
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            yield MenuItem::linkToDashboard('Gestion des users', 'fa fa-user');
            yield MenuItem::linkToCrud('Tous les logements', 'fas fa-home', Logements::class);
            yield MenuItem::linkToCrud('Tous les messages', 'fas fa-envelope', Messages::class);
            yield MenuItem::linkToCrud('Toutes les réservations', 'fas fa-calendar', Booking::class);
        }

        // Pour ROLE_HOTE
        if (in_array('ROLE_HOTE', $user->getRoles())) {
            yield MenuItem::linkToCrud('Mes Logements', 'fas fa-home', Logements::class);
            yield MenuItem::linkToCrud('Messages', 'fas fa-envelope', Messages::class);
            yield MenuItem::linkToCrud('Réservations', 'fas fa-book', Booking::class);
        }



        // Pour ROLE_EMPLOYE
        if (in_array('ROLE_EMPLOYE', $user->getRoles())) {
            yield MenuItem::linkToCrud('Messages', 'fas fa-envelope', Messages::class);
            yield MenuItem::linkToCrud('Mes utilisateurs', 'fa fa-user', User::class);
            yield MenuItem::linkToCrud('Réservations', 'fas fa-book', Booking::class);
            yield MenuItem::linkToCrud('Logements', 'fas fa-home', Logements::class);
        }
    }


    public function configureUserMenu(UserInterface $user): UserMenu
    {
        if (!$user instanceof User) {
            throw new \LogicException('The user is not of the expected type.');
        }

        $menu = UserMenu::new()
            ->setName($user->getFirstName() . ' ' . $user->getLastName())
            ->displayUserName(true);

        if ($user->getImageProfil()) {
            $menu->setAvatarUrl('/img/' . $user->getImageProfil());
        } else {
            $menu->setGravatarEmail($user->getEmail());
        }

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        $profileEditUrl = $adminUrlGenerator
            ->setController(UserCrudController::class)
            ->setAction('edit')
            ->setEntityId($user->getId())
            ->generateUrl();

        $menu->addMenuItems([
            MenuItem::linkToUrl('Mon Compte', 'fa fa-id-card', $profileEditUrl),
            MenuItem::section(),
            MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
        ]);

        return $menu;
    }
}





// Option 2. You can make your dashboard redirect to different pages depending on the user
//
// if ('jane' === $this->getUser()->getUsername()) {
//     return $this->redirect('...');
// }

// Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
// (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
//
// return $this->render('some/path/my-dashboard.html.twig');
