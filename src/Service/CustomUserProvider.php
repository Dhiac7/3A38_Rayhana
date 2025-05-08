<?php
namespace App\Service;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class CustomUserProvider implements UserProviderInterface
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

   
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $identifier]);
    }

    public function refreshUser(UserInterface $user)
    {
        return $user;
    }

   
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
