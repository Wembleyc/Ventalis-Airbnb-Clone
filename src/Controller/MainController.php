<?php

namespace App\Controller;



use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;


class MainController extends AbstractController
{

    
    #[Route('/', name: 'main')]
    public function hometest()
    {

        $user = $this->getUser();

        $name = 'Wembley';
        $age = 29;
        $country = 'Brésil';

        return $this->render('main.html.twig', [
            'name' => $name,
            'age' => $age,
            'country' => $country,
            'is_connected' => $user !== null, // Check if the user is logged in

        ]);
    }

    #[Route('parrainezvosproches', name: 'parrainezvosproches')]
    public function parrainezvosproche()
    {
        // return new Response('Title: Joelma');
        return $this->render('parrainezvosproches.html.twig');

    }
    #[Route('about', name: 'about')]
    public function about()
    {
        // return new Response('Title: Joelma');
        return $this->render('about.html.twig');

    }
    #[Route('decouvrir', name: 'decouvrir')]
    public function decouvrir()
    {
        // return new Response('Title: Joelma');
        return $this->render('decouvrir.html.twig');

    }
    #[Route('carte-cadeau', name: 'carte-cadeau')]
    public function carteCadeau()
    {
        // return new Response('Title: Joelma');
        return $this->render('carte-cadeau.html.twig');

    }


}