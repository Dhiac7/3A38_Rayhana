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
    
    return $this->redirectToRoute('app_user_loginback');
}

}
