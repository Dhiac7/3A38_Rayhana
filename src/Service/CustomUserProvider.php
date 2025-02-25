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

    /**
     * Load user by identifier (email or username)
     */
    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        // Assuming the identifier is the email
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $identifier]);
    }

    public function refreshUser(UserInterface $user)
    {
        // Refresh the user from the database if needed
        return $user;
    }

    /**
     * Return true if this provider supports the User class
     */
    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
