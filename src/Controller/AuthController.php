<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\Security\Core\Security;

class AuthController extends AbstractController
{
    // Authentification et génération du token JWT
    #[Route('/api/login', name: 'api_login', methods: ['POST'])]
    public function login(Request $request, UserPasswordEncoderInterface $passwordEncoder, JWTManager $JWTManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Vérification des données de la requête
        if (!isset($data['email']) || !isset($data['password'])) {
            return new JsonResponse(['message' => 'Email and password required'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $data['email']]);

        if (!$user) {
            return new JsonResponse(['message' => 'Identifiant ou mot de passe incorrect'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Vérification du mot de passe
        if (!$passwordEncoder->isPasswordValid($user, $data['password'])) {
            return new JsonResponse(['message' => 'Identifiant ou mot de passe incorrect'], JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Génération du token JWT
        $token = $JWTManager->create($user);

        return new JsonResponse(['token' => $token], JsonResponse::HTTP_OK);
    }
}
