<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    /**
     * @Route("/message", name="app_message", methods={"GET", "POST"})
     */
    public function index(Request $request): Response
    {
        if ($request->isMethod('POST')) {
            $userMessage = $request->request->get('user_message');

            // Process the user's message and get the bot response
            $botResponse = $this->getBotResponse($userMessage);

            // Assuming you want to return a JSON response for the bot response
            return $this->json(['botResponse' => $botResponse]);
        }

        // Handle the GET request
        return $this->render('message/index.html.twig', [
            'controller_name' => 'MessageController',
        ]);
    }

    private function getBotResponse(string $userMessage): string
    {
        // Implement the logic to generate the bot's response based on the user's message.
        // For this example, we'll use a simple bot that just echoes the user's message.
        return "Bot: You said: " . $userMessage;
    }
}
