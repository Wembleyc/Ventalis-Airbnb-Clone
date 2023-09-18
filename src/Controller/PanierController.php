<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Panier;
use App\Entity\Product;
use App\Entity\PanierProduct;

class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        
        if (null === $user) {
            // Redirect to login page, or show a message, or throw an exception
            throw $this->createNotFoundException('The user is not logged in.');
        }
    
        $panier = $user->getPanier();
        $panierProducts = $panier ? $panier->getPanierProducts() : [];

        // Or if you want to get the products, map the PanierProduct entities to Product entities
    
        return $this->render('panier/index.html.twig', [
            'controller_name' => 'PanierController',
            'panierProducts' => $panierProducts,
        ]);
    }
    /**
     * @Route("/panier/add/{id}", name="cart_add")
     */
    public function add($id, Request $request){
        $session = $request->getSession();
        $panier = $session->get('panier', []);
        $panier[$id] =1;
        $session->set('panier', $panier);

        dd($session->get('panier'));

    }
    
 
    

}