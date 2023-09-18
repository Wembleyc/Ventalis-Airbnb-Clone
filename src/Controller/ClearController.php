<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ClearController extends AbstractController
{
    #[Route('/clear', name: 'app_clear')]
    public function index(): Response
    {
        return $this->render('clear/index.html.twig', [
            'controller_name' => 'ClearController',
        ]);
    }
}
