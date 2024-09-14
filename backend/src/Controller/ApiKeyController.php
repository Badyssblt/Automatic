<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ApiKeyController extends AbstractController
{
    #[Route('/api/apiKey', name: 'app_api_key')]
    public function index(): Response
    {
        $user = $this->getUser();

        if(!$user) return $this->json(['message' => 'Veuillez vous connectez'], Response::HTTP_UNAUTHORIZED);

        $apiKeys = $user->getApiKeys()->map(function ($apiKey) {
            return $apiKey->getToken();
        });

        return $this->json(['keys' => $apiKeys->toArray()]);
    }
}
