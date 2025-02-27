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


#[Route('/user')]
final class UserController extends AbstractController
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
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
            $error = 'Vous avez un ban , Accès refusé';
        } else {
            // If another session is active, force logout previous session
            if ($user->getSessionId() !== null && $user->getSessionId() !== $session->getId()) {
                $error = 'Une session est déjà active pour cet utilisateur.';
            } else {
                // Ensure no other user is logged in
                if ($session->has('user_id') && $session->get('user_id') !== $user->getId()) {
                    $error = 'Un autre utilisateur est déjà connecté. Veuillez vous déconnecter avant de continuer.';
                } else {
                    // Set session data
                    $session->set('user_id', $user->getId());
                    $user->setSessionId($session->getId());
                    $user->setStatut('actif');

                    $entityManager->flush();

                    return $this->redirectToRoute('role_interface', ['role' => $user->getRole()]);
                }
            }
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

    if ($loggedInUserId) {
        $user = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if ($user) {
            $user->setSessionId(null);
            $user->setStatut('inactif');
            $entityManager->flush();
        }
    }

    $session->clear();
    User::setCurrentUser(null);

    return $this->redirectToRoute('app_user_login');
}



    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager,NotificationService $notificationService,UserPasswordHasherInterface $passwordHasher, SessionInterface $session,SluggerInterface $slugger): Response
{
    $user = new User();
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $photoFile = $form->get('photo')->getData();

        if ($photoFile instanceof UploadedFile) {
            dump($photoFile); 
            $uploadsDirectory = $this->getParameter('uploads_directory'); 
            $newFilename = uniqid().'.'.$photoFile->guessExtension();

            $photoFile->move($uploadsDirectory, $newFilename);

            $user->setPhoto($newFilename);
        } else {
            dump('No file uploaded'); 
        }

        $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
        $user->setPassword($hashedPassword);

        $user->setRole('client');

        $slug = $slugger->slug($user->getNom() . ' ' . $user->getPrenom())->lower();
        $user->setSlug($slug);

        $user->setCreatedAt(new \DateTime());

    
    /*  $currentUser = $this->getUser();  
        if ($currentUser) {
            $user->setCreatedBy($currentUser);  
        }*/
        $entityManager->persist($user);
        $entityManager->flush();

         // Send a notification to the admin
        $notificationMessage = sprintf('New user created: %s %s', $user->getNom(), $user->getPrenom());
        $notificationService->createNotification($notificationMessage);
        return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('user/new.html.twig', [
        'user' => $user,
        'form' => $form,
    ]);
}

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
    
        //dump($form->getErrors(true));
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle Photo Upload
            $photoFile = $form->get('photo')->getData();
            if ($photoFile instanceof UploadedFile) {
                $uploadsDirectory = $this->getParameter('uploads_directory'); 
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move($uploadsDirectory, $newFilename);
                $user->setPhoto($newFilename);
            }
    
            $plainPassword = $form->get('mdp')->getData();
            if (!empty($plainPassword)) { 
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('user_profile', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
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
