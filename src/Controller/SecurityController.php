<?php
namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use App\Service\GoogleAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Repository\UserRepository;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


class SecurityController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google')]
    public function connectGoogle(ClientRegistry $clientRegistry): RedirectResponse
    {
        return $clientRegistry->getClient('google')->redirect(
            ['email', 'profile'], // Scopes
            [] // Options (empty array if not needed)
        );
    }
    
    #[Route('/connect/google/check', name: 'connect_google_check', methods: ['GET', 'POST'])]
    public function connectGoogleCheck(Request $request): void
    {
        // This method should never be executed because the GoogleAuthenticator
        // intercepts requests to this route.
        throw new \LogicException('This route is handled by the GoogleAuthenticator.');
    }
}