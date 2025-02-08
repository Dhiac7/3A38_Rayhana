<?php
namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/interface')]
class InterfaceController extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    #[Route('/client', name: 'client_interface')]
    public function clientDashboard(): Response
    {
        if (!$this->security->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/client.html.twig');
    }

    #[Route('/fermier', name: 'fermier_interface')]
    public function fermierDashboard(): Response
    {
        if (!$this->security->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/fermier.html.twig');
    }

    #[Route('/agriculteur', name: 'agriculteur_interface')]
    public function agriculteurDashboard(): Response
    {
        if (!$this->security->getUser()) {
            // Redirect to homepage if not logged in
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/agriculteur.html.twig');
    }

    #[Route('/inspecteur', name: 'inspecteur_interface')]
    public function inspecteurDashboard(): Response
    {
        if (!$this->security->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/inspecteur.html.twig');
    }

    #[Route('/livreur', name: 'livreur_interface')]
    public function livreurDashboard(): Response
    {
        if (!$this->security->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        return $this->render('user/livreur.html.twig');
    }

    #[Route('/profile', name: 'user_profile')]
    public function userProfile(SessionInterface $session): Response
    {
        $user = $this->security->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_user_login');
        }

        return $this->render('user/profile.html.twig', [
            'user' => $user,  
        ]);
    }
}
