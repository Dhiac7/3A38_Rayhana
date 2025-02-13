<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;


final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(SessionInterface $session): Response
    {
        $user = $session->get('user');
        if (!$user) {
            return $this->redirectToRoute('app_user_loginback');
        }
        return $this->render('baseAdmin.html.twig', [
            'controller_name' => 'DashboardController',
            'user' => $user,  // Passer user
        ]);
    }
}
