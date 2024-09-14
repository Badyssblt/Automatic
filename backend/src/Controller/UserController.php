<?php

namespace App\Controller;

use App\Entity\ApiKey;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;

class UserController extends AbstractController
{
    #[Route('/api/verification', name: 'app_user', methods: ['POST'])]
    public function verification(Request $request, EntityManagerInterface $manager, UserRepository $userRepository): Response
    {
        $code = $request->getPayload()->get('code');

        if(empty($code)) {
            return $this->json(['message' => 'Le code est vide'], Response::HTTP_BAD_REQUEST);
        }

        $email = $request->getPayload()->get('email');

        $user = $userRepository->findOneBy(['email' => $email]);

        if(!$user) return $this->json(['message' => 'Veuillez vous connectez'], Response::HTTP_UNAUTHORIZED);

        if($user->getVerificationCode() !== $code) {
            return $this->json(['message' => 'Le code est incorrect'], Response::HTTP_BAD_REQUEST);
        }

        $user->setVerified(true);

        $apiKey = new ApiKey();
        $apiKey->setUser($user);
        $apiKey->setToken(Uuid::v4());

        $manager->persist($user);
        $manager->persist($apiKey);

        $manager->flush();

        return $this->json(['message' => 'Vérification réussi'], Response::HTTP_OK);
    }
}
