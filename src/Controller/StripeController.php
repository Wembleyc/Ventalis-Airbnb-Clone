<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\CardException;

class StripeController extends AbstractController
{
    #[Route('/paiement', name: 'payment')]
    public function showPaymentForm()
    {
        $stripePublicKey = $this->getParameter('stripe_public_key');

        return $this->render('payment.html.twig', [
            'stripe_public_key' => $stripePublicKey
        ]);
    }

    #[Route("/create_payment", name: "create_payment", methods: ["POST"])]
    public function createPayment(Request $request): Response
    {
        $secretKey = $this->getParameter('stripe_secret_key');
        Stripe::setApiKey($secretKey);

        $paymentToken = $request->request->get('payment_token');

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => 2000000,
                'currency' => 'eur',
                'payment_method' => $paymentToken,
                'confirmation_method' => 'manual',
                'confirm' => true,
            ]);

            return new Response('Paiement créé avec succès !');
        } catch (CardException $e) {
            return new Response('Erreur lors du paiement : ' . $e->getMessage());
        } catch (ApiErrorException $e) {
            return new Response('Erreur Stripe : ' . $e->getMessage());
        }
    }
}
