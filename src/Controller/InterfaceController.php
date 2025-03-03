<?php
namespace App\Controller; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Entity\Message;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
use App\Repository\MessageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Service\MailService;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use App\Service\EmailMessage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\JsonResponse;


#[Route('/interface')]
class InterfaceController extends AbstractController
{
    private MailerInterface $mailer;


    public function __construct(MailService $twilioService,MailerInterface $mailer)
    {
        //$this->twilioService = $twilioService;
        $this->mailer = $mailer;

    }

    #[Route('/{role}', name: 'role_interface', requirements: ['role' => 'client|fermier|agriculteur|inspecteur|livreur'])]
    public function roleDashboard(SessionInterface $session, string $role, EntityManagerInterface $entityManager,HttpClientInterface $httpClient,PaginatorInterface $paginator, 
    Request $request): Response
{
    {
        $userId = $session->get('user_id');

       /* if (!$userId) {
            return $this->redirectToRoute('app_home');
        }
    */
        $loggedInUser = $entityManager->getRepository(User::class)->find($userId);
    
        /*if (!$loggedInUser) {
            $session->remove('user_id');
            return $this->redirectToRoute('app_user_login');
        }
    */
       // User::setCurrentUser($loggedInUser);

        if ($loggedInUser->getRole() !== $role) {
            $this->addFlash('error', 'You do not have permission to access this page.');
            return $this->redirectToRoute('role_interface', ['role' => $loggedInUser->getRole()]);
        }
            $response = $httpClient->request('GET', 'https://newsapi.org/v2/everything', [
                'query' => [
                    'q' => 'agriculture',
                    'language' => 'fr',
                    'apiKey' => '54c165e45e684e5c9dc19c4afdd0b8fb', 
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $newsData = $response->toArray();
                $articles = $newsData['articles'] ?? [];
            } else {
                $articles = [];
            }

            $paginatedArticles = $paginator->paginate(
                $articles, 
                $request->query->getInt('page', 1), 
                5 
            );

        return $this->render("user/{$role}.html.twig", [
            'loggedInUser' => $loggedInUser,
            'articles' => $paginatedArticles, 
 
        ]);
    }
}
    #[Route('/fermier', name: 'fermier_interface')]
    public function fermierDashboard(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->redirectToRoute('app_home');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($userId);
    
        if (!$loggedInUser) {
            $session->remove('user_id');
            return $this->redirectToRoute('app_user_login');
        }
    
        User::setCurrentUser($loggedInUser);

        return $this->render('user/fermier.html.twig');
    }

    #[Route('/agriculteur', name: 'agriculteur_interface')]
    public function agriculteurDashboard(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->redirectToRoute('app_home');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($userId);
    
        if (!$loggedInUser) {
            $session->remove('user_id');
            return $this->redirectToRoute('app_user_login');
        }
    
        User::setCurrentUser($loggedInUser);

        return $this->render('user/agriculteur.html.twig');
    }

    #[Route('/inspecteur', name: 'inspecteur_interface')]
    public function inspecteurDashboard(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->redirectToRoute('app_home');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($userId);
    
        if (!$loggedInUser) {
            $session->remove('user_id');
            return $this->redirectToRoute('app_user_login');
        }
    
        User::setCurrentUser($loggedInUser);
        return $this->render('user/inspecteur.html.twig');
    }

    #[Route('/livreur', name: 'livreur_interface')]
    public function livreurDashboard(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->redirectToRoute('app_home');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($userId);
    
        if (!$loggedInUser) {
            $session->remove('user_id');
            return $this->redirectToRoute('app_user_login');
        }
    
        User::setCurrentUser($loggedInUser);

        return $this->render('user/livreur.html.twig');
    }

    #[Route('/profile/{slug}', name: 'user_profile')]
    public function userProfile(string $slug,SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $userId = $session->get('user_id');

        if (!$userId) {
            return $this->redirectToRoute('app_home');
        }
    
        //$loggedInUser = $entityManager->getRepository(User::class)->find($userId);
        $loggedInUser = $entityManager->getRepository(User::class)->findOneBy(['slug' => $slug]);

        if (!$loggedInUser) {
            $session->remove('user_id');
            return $this->redirectToRoute('app_user_login');
        }
    
        User::setCurrentUser($loggedInUser);

        return $this->render('user/profile.html.twig', [
            'loggedInUser' => $loggedInUser,
        ]);
    }
 /*///////////////////////////////////////////////////////////////////////////////////////////////////////////*/   
    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(Request $request, UserRepository $userRepository): Response
    {
        $showVerificationForm = false;
        $showResetForm = false;

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email'); 
            $user = $userRepository->findOneByEmail($email); 

            if ($user) {
                $session = $request->getSession();
                $session->start();

                $verificationCode = rand(1000, 9999);
                $session->set('verification_code', $verificationCode);
                $session->set('email', $email);

                $emailMessage = (new Email())
                    ->from('routou200@gmail.com')
                    ->to($email)
                    ->subject('Your Verification Code')
                    ->text('Votre code de vérification: ' . $verificationCode)
                    ->html('<p>Votre code de vérification est: <strong>' . $verificationCode . '</strong></p>');

                try {
                    $this->mailer->send($emailMessage);
                    $showVerificationForm = true;

                    return $this->render('user/forgot_password.html.twig', [
                        'showVerificationForm' => $showVerificationForm,
                        'showResetForm' => $showResetForm, 
                    ]);
                } catch (\Exception $e) {
                    return new Response('Failed to send email. Please try again later.');
                }
            } else {
                return new Response('User not found!');
            }
        }

        return $this->render('user/forgot_password.html.twig', [
            'showVerificationForm' => $showVerificationForm,
            'showResetForm' => $showResetForm, 
        ]);
    }

    #[Route('/verify-code', name: 'verify_code')]
    public function verifyCode(Request $request, UserRepository $userRepository): Response
    {
        $verificationCode = $request->request->get('verification_code');
        $session = $request->getSession();
        $storedCode = $session->get('verification_code');
        $email = $session->get('email');
        $showResetForm = false;

        if ($verificationCode == $storedCode) {
            $showResetForm = true;
        } else {
            return new Response('Invalid verification code.');
        }

        return $this->render('user/forgot_password.html.twig', [
            'showVerificationForm' => false,  
            'showResetForm' => $showResetForm,          
            'email' => $email    
        ]);
    }

    #[Route('/reset-password', name: 'reset_password')]
    public function resetPassword(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $email = $request->request->get('email'); 

        if (!$email) {
            return new Response('Unauthorized access! No email provided.');
        }

        $user = $userRepository->findOneByEmail($email);

        if (!$user) {
            return new Response('User not found!');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('newPassword');

            if (strlen($newPassword) < 6) {
                return new Response('Password must be at least 6 characters long.');
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $user->setMdp($newPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_login');
        }

        return $this->render('user/forgot_password.html.twig', ['email' => $email]);
    }

 /*///////////////////////////////////////////////////////////////////////////////////////////////*/
    #[Route('/admin-send-email', name: 'admin_send_email')]
    public function AdminsendEmail(Request $request, UserRepository $userRepository): Response
    {
        $showVerificationForm = false;
        $showResetForm = false;
    
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email'); 
            $user = $userRepository->findOneByEmail($email); 
    
            if ($user) {
                if ($user->getRole() === 'agriculteur') {
                    $session = $request->getSession();
                    $session->start();
    
                    $verificationCode = rand(1000, 9999);
                    $session->set('verification_code', $verificationCode);
                    $session->set('email', $email);
    
                    $emailMessage = (new Email())
                        ->from('routou200@gmail.com')
                        ->to($email)
                        ->subject('Your Verification Code')
                        ->text('Votre code de vérification: ' . $verificationCode)
                        ->html('<p>Votre code de vérification est: <strong>' . $verificationCode . '</strong></p>');
    
                    try {
                        $this->mailer->send($emailMessage);
                        $showVerificationForm = true;
    
                        return $this->render('user/forgot_password.html.twig', [
                            'showVerificationForm' => $showVerificationForm,
                            'showResetForm' => $showResetForm, 
                        ]);
                    } catch (\Exception $e) {
                        return new Response(' Echec d envoi de cet e-mail. Veuillez réessayer plus tard.');
                    }
                } else {
                    return new Response('Vous êtes pas autorisé à réinitialiser le mot de passe.');
                }
            } else {
                return new Response('Utilisateur inexistant!');
            }
        }
    
        return $this->render('user/admin_forgot_password.html.twig', [
            'showVerificationForm' => $showVerificationForm,
            'showResetForm' => $showResetForm, 
        ]);
    }
    #[Route('/admin-verify-code', name: 'admin_verify_code')]
    public function AdminverifyCode(Request $request, UserRepository $userRepository): Response
    {
        $verificationCode = $request->request->get('verification_code');
        $session = $request->getSession();
        $storedCode = $session->get('verification_code');
        $email = $session->get('email');
        $showResetForm = false;

        if ($verificationCode == $storedCode) {
            $showResetForm = true;
        } else {
            return new Response('Invalid verification code.');
        }

        return $this->render('user/admin_forgot_password.html.twig', [
            'showVerificationForm' => false,  
            'showResetForm' => $showResetForm,          
            'email' => $email    
        ]);
    }

    #[Route('/admin-reset-password', name: 'admin_reset_password')]
    public function AdminresetPassword(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $email = $request->request->get('email'); 

        if (!$email) {
            return new Response('Unauthorized access! No email provided.');
        }

        $user = $userRepository->findOneByEmail($email);

        if (!$user) {
            return new Response('User not found!');
        }

        if ($request->isMethod('POST')) {
            $newPassword = $request->request->get('newPassword');

            if (strlen($newPassword) < 8) {
                return new Response('Le mot de passe doit avoir au moins 8 caractères.');
            }

            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);

            $user->setMdp($newPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_login');
        }

        return $this->render('user/admin_forgot_password.html.twig', ['email' => $email]);
    }
 /*//////////////////////////////////////////////////////////////////////////////////////////*/   
    #[Route('/client', name: 'client_interface')]
    public function clientDashboard(SessionInterface $session, EntityManagerInterface $entityManager, HttpClientInterface $httpClient,PaginatorInterface $paginator, // Inject the paginator
    Request $request): Response
{
    $loggedInUserId = $session->get('user_id');

    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_login');
    }

    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);

    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_login');
    }

    $response = $httpClient->request('GET', 'https://newsapi.org/v2/everything', [
        'query' => [
            'q' => 'agriculture', 
            'language' => 'fr',
            'apiKey' => '54c165e45e684e5c9dc19c4afdd0b8fb', 
        ],
    ]);

    if ($response->getStatusCode() === 200) {
        $newsData = $response->toArray();
        $articles = $newsData['articles'] ?? [];
    } else {
        $articles = [];
    }

    $paginatedArticles = $paginator->paginate(
        $articles, 
        $request->query->getInt('page', 1),         
        5 
    );

    return $this->render('user/news.html.twig', [
        'loggedInUser' => $loggedInUser,
        'articles' => $paginatedArticles, 
    ]);
}


/*--------------------------------------------------------------------*/
#[Route('/chatadmin', name: 'admin_chat')]
public function adminChat(SessionInterface $session, EntityManagerInterface $entityManager, MessageRepository $messageRepository): Response
{
    $userId = $session->get('user_id');

    if (!$userId) {
        return $this->redirectToRoute('app_dashboard');
    }

    $loggedInUser = $entityManager->getRepository(User::class)->find($userId);

    if (!$loggedInUser) {
        $session->remove('user_id');
        return $this->redirectToRoute('app_user_loginback');
    }

    User::setCurrentUser($loggedInUser);

    // Fetch all users except the logged-in user and users with the role 'client'
    $users = $entityManager->getRepository(User::class)->createQueryBuilder('u')
        ->where('u.id != :loggedInUserId')
        ->andWhere('u.role != :clientRole')
        ->setParameter('loggedInUserId', $loggedInUser->getId())
        ->setParameter('clientRole', 'client')
        ->getQuery()
        ->getResult();

    // If no users are found, redirect or handle accordingly
    if (empty($users)) {
        return $this->render('message/admin.html.twig', [
            'loggedInUser' => $loggedInUser,
            'role' => $loggedInUser->getRole(),
            'users' => [],
            'adminId' => $loggedInUser->getId(),
            'selectedUser' => null,
            'conversation' => [],
            'selectedUserId' => null,
        ]);
    }

    // Select the first user in the list as the default selected user
    $selectedUserId = $users[0]->getId();
    $selectedUser = $entityManager->getRepository(User::class)->find($selectedUserId);

    // Fetch the conversation between the logged-in user and the selected user
    $conversation = $messageRepository->findConversation($loggedInUser->getId(), $selectedUserId);

    return $this->render('message/admin.html.twig', [
        'loggedInUser' => $loggedInUser,
        'role' => $loggedInUser->getRole(),
        'users' => $users,
        'adminId' => $loggedInUser->getId(),
        'selectedUser' => $selectedUser,
        'conversation' => $conversation,
        'selectedUserId' => $selectedUserId,
    ]);
}
/*#[Route('/chatemploye', name: 'employe_chat')]
public function employeeChat(SessionInterface $session, EntityManagerInterface $entityManager, MessageRepository $messageRepository): Response
{
    $userId = $session->get('user_id');

    if (!$userId) {
        return $this->redirectToRoute('app_home');
    }

    $loggedInUser = $entityManager->getRepository(User::class)->find($userId);

    if (!$loggedInUser) {
        $session->remove('user_id');
        return $this->redirectToRoute('app_user_login');
    }

    User::setCurrentUser($loggedInUser);

    $agriculteurs = $entityManager->getRepository(User::class)->findByRole('agriculteur');

    if (empty($agriculteurs)) {
        $this->addFlash('warning', 'No users found with the role "agriculteur".');
        return $this->redirectToRoute('app_home'); 
    }

    $selectedUserId = $agriculteurs[0]->getId();
    $selectedUser = $entityManager->getRepository(User::class)->find($selectedUserId);

    $conversation = $messageRepository->findConversation($loggedInUser->getId(), $selectedUserId);

    return $this->render('message/employe.html.twig', [
        'loggedInUser' => $loggedInUser,
        'role' => $loggedInUser->getRole(),
        'agriculteurs' => $agriculteurs,
        'employeeId' => $loggedInUser->getId(),
        'selectedUser' => $selectedUser,
        'conversation' => $conversation,
        'selectedUserId' => $selectedUserId,
    ]);
}*/
#[Route('/send-message', name: 'send_message', methods: ['POST'])]
public function sendMessage(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): JsonResponse
{
    $messageContent = $request->request->get('message');
    $targetUserId = $request->request->get('targetUserId');
    $senderId = $session->get('user_id');
    $file = $request->files->get('file');

    // Validate required fields
    if (empty($messageContent) || empty($targetUserId) || empty($senderId)) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Message, target user ID, and sender ID are required.'
        ]);
    }

    // Fetch sender and target user entities
    $sender = $entityManager->getRepository(User::class)->find($senderId);
    $target = $entityManager->getRepository(User::class)->find($targetUserId);

    if (!$sender || !$target) {
        return new JsonResponse([
            'status' => 'error',
            'message' => 'Sender or target user not found.'
        ]);
    }

    // Handle file upload (if any)
    $fileUrl = null;
    if ($file) {
        $fileName = uniqid() . '.' . $file->guessExtension();
        $file->move($this->getParameter('files_directory'), $fileName);
        $fileUrl = '/img/files/' . $fileName; // Adjust the path as needed
    }

    // Create and persist the message
    $message = new Message();
    $message->setSender($sender);
    $message->setRecipient($target);
    $message->setContent($messageContent);
    $message->setCreatedAt(new \DateTime());
    $message->setFileUrl($fileUrl);

    $entityManager->persist($message);
    $entityManager->flush();

    // Return the sent message in the response
    return new JsonResponse([
        'status' => 'success',
        'message' => [
            'id' => $message->getId(),
            'content' => $message->getContent(),
            'fileUrl' => $message->getFileUrl(),
            'timestamp' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            'sender' => [
                'id' => $sender->getId(),
                'nom' => $sender->getNom(),
                'photo' => $sender->getPhoto(),
            ],
        ],
    ]);
}

#[Route('/fetch-messages', name: 'fetch_messages', methods: ['GET'])]
public function fetchMessages(Request $request, MessageRepository $messageRepository, SessionInterface $session): JsonResponse
{
    $targetUserId = $request->query->get('targetUserId');
    $senderId = $session->get('user_id');

    $messages = $messageRepository->findMessagesBetweenUsers($senderId, $targetUserId);

    $formattedMessages = [];
    foreach ($messages as $message) {
        $formattedMessage = [
            'sender' => [
                'id' => $message->getSender()->getId(),
                'photo' => $message->getSender()->getPhoto(),
            ],
            'content' => $message->getContent(),
            'timestamp' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
        ];

        if ($message->getFileurl()) {
            $formattedMessage['fileurl'] = $message->getFileurl();
        }

        $formattedMessages[] = $formattedMessage;
    }

    return new JsonResponse([
        'status' => 'success',
        'messages' => $formattedMessages,
    ]);
}
    #[Route('/fetch-user-details', name: 'fetch_user_details', methods: ['GET'])]
    public function fetchUserDetails(Request $request, EntityManagerInterface $entityManager, MessageRepository $messageRepository): JsonResponse
    {
        $targetUserId = $request->query->get('targetUserId');
        $senderId = $request->query->get('senderId'); 
    
        $selectedUser = $entityManager->getRepository(User::class)->find($targetUserId);
    
        if (!$selectedUser) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'User not found.',
            ]);
        }
    
        $conversation = $messageRepository->findConversation($senderId, $targetUserId);
    
        return new JsonResponse([
            'status' => 'success',
            'selectedUser' => [
                'id' => $selectedUser->getId(),
                'nom' => $selectedUser->getNom(),
                'photo' => $selectedUser->getPhoto(),
            ],
            'conversation' => array_map(function ($message) {
                return [
                    'sender' => [
                        'id' => $message->getSender()->getId(),
                        'photo' => $message->getSender()->getPhoto(),
                    ],
                    'content' => $message->getContent(),
                    'timestamp' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
                ];
            }, $conversation),
        ]);
    }

}