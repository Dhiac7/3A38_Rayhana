<?php

namespace App\Controller;
use App\Entity\User;
use App\Service\NotificationService;
use App\Form\UserType;
use App\Form\UserEditType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use App\Service\MailService;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailTrait;
use SymfonyCasts\Bundle\VerifyEmail\Model\VerifyEmailInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mailer\MailerInterface;
use App\Service\EmailVerifier;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mime\Address;
use Symfony\Contracts\Translation\TranslatorInterface;

#[Route('/user')]
final class UserController extends AbstractController
{
    private SluggerInterface $slugger;
    private EmailVerifier $emailVerifier;
    
    public function __construct(SluggerInterface $slugger , EmailVerifier $emailVerifier)
    {
        $this->slugger = $slugger;
        $this->emailVerifier=$emailVerifier;
    }

    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request,UserRepository $userRepository, SessionInterface $session, PaginatorInterface $paginator): Response
    {
        $query = $userRepository->findAll();
        $pagination = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1), 
            4  
        );
        return $this->render('user/index.html.twig', [
            'pagination' => $pagination,
        ]);

    }

    #[Route('/login', name: 'app_user_login', methods: ['GET', 'POST'])]
    public function login(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $error = null;
    
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');
    
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    
            if (!$user || !password_verify($password, $user->getPassword())) {
                $error = 'Invalid email or password';
            } elseif ($user->getRole() === 'agriculteur' || $user->getRole() === 'inspecteur') {
                $error = 'Accès refusé';
            } elseif ($user->getStatut() === 'banni') {
                $error = 'Vous avez un ban, Accès refusé';
            } elseif (!$user->isVerified()) { 
                $error = 'Votre adresse e-mail n\'a pas été vérifiée. Veuillez vérifier votre e-mail pour continuer.';
            } elseif (User::getCurrentUser()!=null) { 
                        $error = 'Un autre utilisateur est déjà connecté. Veuillez vous déconnecter avant de continuer.';
            } elseif ($session->get('user_id')!=null) { 
                $error = 'Un autre utilisateur est déjà connecté. Veuillez vous déconnecter avant de continuer.';
            } else {
                        $session->set('user_id', $user->getId());
                        $user->setStatut('actif');
                        User::setCurrentUser($user);
                        $entityManager->flush();
    
                        return $this->redirectToRoute('role_interface', ['role' => $user->getRole()]);
                    }
            
        }
    
        return $this->render('user/login.html.twig', [
            'error' => $error,
        ]);
    }
    #[Route('/logout', name: 'app_user_logout', methods: ['GET'])]
    public function logout(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $loggedInUserId = $session->get('user_id');
        User::setCurrentUser(null);

        /*if ($loggedInUserId) {
            $user = $entityManager->getRepository(User::class)->find($loggedInUserId);
            if ($user) {
                $entityManager->createQueryBuilder()
                ->update(User::class, 'u')
                ->set('u.SessionId', ':nullValue')
                ->set('u.statut', ':statut')
                ->where('u.id = :userId')
                ->setParameter('nullValue', null)
                ->setParameter('statut', 'inactif')
                ->setParameter('userId', $loggedInUserId)
                ->getQuery()
                ->execute();
                $entityManager->flush();
            }
        }*/
        $session->set('user_id', null);

        //$session->clear();

        return $this->redirectToRoute('app_user_login');
    }
    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, NotificationService $notificationService, UserPasswordHasherInterface $passwordHasher, SessionInterface $session, SluggerInterface $slugger): Response
{
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $photoFile = $form->get('photo')->getData();

        if ($photoFile instanceof UploadedFile) {
            $uploadsDirectory = $this->getParameter('uploads_directory');
            $newFilename = uniqid().'.'.$photoFile->guessExtension();
            $photoFile->move($uploadsDirectory, $newFilename);

            $user->setPhoto($newFilename);
        } else {
            $defaultAvatar = 'img/users/avatar.jpg'; 
            $user->setPhoto($defaultAvatar); 
        }

        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $user->setRole('client');

        $slug = $slugger->slug($user->getNom() . ' ' . $user->getPrenom())->lower();
        $user->setSlug($slug);

        $user->setCreatedAt(new \DateTime());

        $entityManager->persist($user);
        $entityManager->flush();
        $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
        (new TemplatedEmail())
            ->from(new Address('mailer@example.com', 'AcmeMailBot'))
            ->to($user->getEmail())
            ->subject('Please Confirm your Email')
            ->htmlTemplate('user/verify_email.html.twig')
    );

        $notificationMessage = sprintf('New user created: %s %s', $user->getNom(), $user->getPrenom());
        $notificationService->createNotification($notificationMessage);

        return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('user/new.html.twig', [
        'user' => $user,
        'form' => $form,
    ]);
}

#[Route('/verify/email', name: 'app_verify_email')]
public function verifyUserEmail(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
{
    $id = $request->query->get('id'); 

    if (null === $id) {
        return $this->redirectToRoute('app_home');
    }

    $user = $entityManager->getRepository(User::class)->find($id);

    if (null === $user) {
        return $this->redirectToRoute('app_home');
    }

    try {
        $this->emailVerifier->handleEmailConfirmation($request, $user);
    } catch (VerifyEmailExceptionInterface $exception) {
        $this->addFlash('verify_email_error', $exception->getReason());

        return $this->redirectToRoute('app_register');
    }

    $this->addFlash('success', 'Your email address has been verified.');

    return $this->redirectToRoute('app_user_login');
}

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request, 
        EntityManagerInterface $entityManager, 
        UserPasswordHasherInterface $passwordHasher, 
        SessionInterface $session
    ): Response {
        $loggedInUserId = $session->get('user_id');
    
        if (!$loggedInUserId) {
            throw new \Exception("User ID is missing from the session.");
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            throw new \Exception("User with ID $loggedInUserId not found.");
        }
    
        
        $form = $this->createForm(UserEditType::class, $loggedInUser);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
            if ($photoFile instanceof UploadedFile) {
                $uploadsDirectory = $this->getParameter('uploads_directory'); 
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move($uploadsDirectory, $newFilename);
                $loggedInUser->setPhoto($newFilename);
            }
    
            $plainPassword = $form->get('mdp')->getData();
            if (!empty($plainPassword)) { 
                $hashedPassword = $passwordHasher->hashPassword($loggedInUser, $plainPassword);
                $loggedInUser->setPassword($hashedPassword);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('user_profile', ['slug' => $loggedInUser->getSlug()]);//, [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('user/edit.html.twig', [
            'user' => $loggedInUser,
            'form' => $form,
            'loggedInUser' => $loggedInUser,
        ]);
    }
    
    


    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_logout', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }



}
