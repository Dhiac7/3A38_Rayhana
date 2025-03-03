<?php

namespace App\Controller;

use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

#[Route('/messages')]
class MessageController extends AbstractController
{
    #[Route('/', name: 'app_messages', methods: ['GET'])]
    public function index(MessageRepository $messageRepository, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($userId);

        if (!$loggedInUser) {
            $session->remove('user_id');
            return $this->redirectToRoute('app_user_loginback');
        }

        return $this->render('message/index.html.twig', [
            'sentMessages' => $messageRepository->findBy(['sender' => $loggedInUser]),
            'receivedMessages' => $messageRepository->findBy(['recipient' => $loggedInUser]),
            'loggedInUser' => $loggedInUser,
        ]);
    }

    #[Route('/new', name: 'app_message_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($userId);

        if (!$loggedInUser) {
            $session->remove('user_id');
            return $this->redirectToRoute('app_user_loginback');
        }

        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message->setSender($loggedInUser);
            $message->setCreatedAt(new \DateTime());

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_messages');
        }

        return $this->render('message/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}