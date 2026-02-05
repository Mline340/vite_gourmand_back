<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Attribute\Route;
use DateTimeImmutable;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpKernel\Attribute\AsController;
use App\Dto\RegistrationRequest;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use ApiPlatform\Metadata\Post;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;



#[AsController]
final class SecurityController extends AbstractController
{
    public function __construct(
        private EntityManagerInterface $manager, 
        private SerializerInterface $serializer
    ) {}

    public function __invoke(Request $request, UserPasswordHasherInterface $passwordHasher): JsonResponse
    {
        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');
        $user->setPassword($passwordHasher->hashPassword($user, $user->getPassword()));
        $user->setCreatedAt(new DateTimeImmutable());
        
        $this->manager->persist($user);
        $this->manager->flush();
        
        return new JsonResponse(['message' => 'Inscription réussie'], 201);
    }
}
