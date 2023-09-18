<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Form\ProductFormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;



class ProductController extends AbstractController
{
    #[Route('/product', name: 'app_product')]
    public function index(EntityManagerInterface $entityManager)
    {
        $products = $entityManager->getRepository(Product::class)->findBy(['valide' => true]);

        return $this->render('products/index.html.twig', [
            'products' => $products
        ]);
    }
   #[Route('/product/new', name: 'app_product_new')]
    public function new(Request $request, EntityManagerInterface $manager)
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        $error = null;

        if ($form->isSubmitted()) {
            if (strlen($form->get('description')->getData()) > 250) {
                $error = 'La description ne peut pas dépasser 250 caractères.';
            } else if ($form->isValid()) {
                $product->setValide(true);
                $manager->persist($product);
                $manager->flush();
                $this->addFlash('success', 'Produit créé avec succès ! Produit : ' . $product->getName());
                return $this->redirectToRoute('app_product');
            }
        }
        

        return $this->render('products/new.html.twig', [
            'form' => $form->createView(),
            'error' => $error
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_show')]
    public function show($id, EntityManagerInterface $entityManager)
    {
        $product = $entityManager->getRepository(Product::class)->find($id);



        return $this->render('products/show.html.twig', [
            'product' => $product
        ]);
    }
    /**
     * @Route("/product/description-length", name="product_description_length", methods={"GET"})
     */
    public function getDescriptionLength(Request $request): JsonResponse
    {
        $description = $request->query->get('description', '');
        $length = mb_strlen($description);

        return new JsonResponse(['length' => $length]);
    }


}