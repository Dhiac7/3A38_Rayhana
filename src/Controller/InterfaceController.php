<?php
namespace App\Controller; 
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Repository\UserRepository;
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

        if (!$userId) {
            return $this->redirectToRoute('app_home');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($userId);
    
        if (!$loggedInUser) {
            $session->remove('user_id');
            return $this->redirectToRoute('app_user_login');
        }
    
        User::setCurrentUser($loggedInUser);

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
                    ->text('Your verification code is: ' . $verificationCode)
                    ->html('<p>Your verification code is: <strong>' . $verificationCode . '</strong></p>');

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


}