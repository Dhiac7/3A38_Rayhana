<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;


final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
public function index(SessionInterface $session, EntityManagerInterface $entityManager): Response 
{
    $userId = $session->get('admin_user_id');

    if (!$userId) {
        return $this->redirectToRoute('app_user_loginback');
    }

    $loggedInUser = $entityManager->getRepository(User::class)->find($userId);

    if (!$loggedInUser) {
        $session->remove('admin_user_id');
        return $this->redirectToRoute('app_user_loginback');
    }

    User::setCurrentUser($loggedInUser);

    return $this->render('baseAdmin.html.twig', [
        'controller_name' => 'DashboardController',
        'loggedInUser' => User::getCurrentUser(),
    ]);
}

}
