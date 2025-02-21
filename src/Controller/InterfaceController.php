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
    public function roleDashboard(SessionInterface $session, string $role, EntityManagerInterface $entityManager): Response
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

        return $this->render("user/{$role}.html.twig", [
            'loggedInUser' => $loggedInUser, 
        ]);
    }

    #[Route('/client', name: 'client_interface')]
    public function clientDashboard(SessionInterface $session, EntityManagerInterface $entityManager): Response
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

        return $this->render('user/client.html.twig', [
            'loggedInUser' => $loggedInUser, 
        ]);
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

    /*#[Route('/send-sms', name: 'send_sms')]
    public function sendSms(Request $request, UserRepository $userRepository): Response
    {
        $showVerificationForm = false; 
        $showResetForm = false; 

        if ($request->isMethod('POST')) {
            $tel = $request->request->get('tel'); 
            $user = $userRepository->findByPhoneNumber($tel);
    
            if ($user) {
                $session = $request->getSession();
                $session->start();
    
                $verificationCode = rand(1000, 9999);
                $session->set('verification_code', $verificationCode);
                $session->set('tel', $tel);
    
                try {
                    $this->twilioService->sendSms($tel, 'Your verification code is: ' . $verificationCode);
                    $showVerificationForm = true;
    
                    return $this->render('user/forgot_password.html.twig', [
                        'showVerificationForm' => $showVerificationForm,
                        'showResetForm' => $showResetForm, 
                    ]);
                } catch (\Exception $e) {
                    return new Response('Failed to send SMS. Please try again later.');
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
        $phoneNumber = $session->get('tel');
        $showResetForm = false; 

        if ($verificationCode == $storedCode) {
            $showResetForm = true;
        } else {
            return new Response('Invalid verification code.');
        }

        return $this->render('user/forgot_password.html.twig', [
            'showVerificationForm' => false,  
            'showResetForm' => $showResetForm,          
            'phoneNumber' => $phoneNumber    
        ]);
    }

        #[Route('/reset-password', name: 'reset_password')]
    public function resetPassword(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $phoneNumber = $request->request->get('phoneNumber'); 

        if (!$phoneNumber) {
            return new Response('Unauthorized access! No phone number provided.');
        }

        $user = $userRepository->findByPhoneNumber($phoneNumber);

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

            //return new Response('Password reset successfully! You can now log in.');
            return $this->redirectToRoute('app_user_login'); 

        }

        return $this->render('user/forgot_password.html.twig', ['phoneNumber' => $phoneNumber]);
    }

    #[Route('/test-email', name: 'test_email')]
    public function testEmail(MessageBusInterface $bus): Response
    {
        // Create the email
        $email = (new Email())
            ->from('routou200@gmail.com')
            ->to('routou200@gmail.com')
            ->subject('Test Email from Symfony')
            ->text('This is a test email sent from Symfony!')
            ->html('<p>This is a <strong>test email</strong> sent from Symfony!</p>');

        // Dispatch the email message to the bus
        $bus->dispatch(new EmailMessage($email));

        // Normally, the worker process should be running in the background. No need to start it here.
        // Make sure your Messenger worker is running continuously using something like:
        //   php bin/console messenger:consume async --no-interaction
        //   You can set it up to run as a daemon with tools like Supervisor.

        return new Response('Email has been dispatched to the message bus, and the worker will process it in the background.');
    }*/
    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(Request $request, UserRepository $userRepository): Response
    {
        $showVerificationForm = false;
        $showResetForm = false;

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email'); 
            $user = $userRepository->findOneByEmail($email);  // Searching for the user by email

            if ($user) {
                $session = $request->getSession();
                $session->start();

                $verificationCode = rand(1000, 9999);
                $session->set('verification_code', $verificationCode);
                $session->set('email', $email);

                // Create email message
                $emailMessage = (new Email())
                    ->from('routou200@gmail.com')
                    ->to($email)
                    ->subject('Your Verification Code')
                    ->text('Your verification code is: ' . $verificationCode)
                    ->html('<p>Your verification code is: <strong>' . $verificationCode . '</strong></p>');

                try {
                    // Send the email using Symfony Mailer
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

}