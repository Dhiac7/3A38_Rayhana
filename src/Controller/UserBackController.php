<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;
use App\Form\UserEditType;
use App\Form\UserAdminEditType;
use App\Form\UserAdminType;
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


#[Route('/userback')]
final class UserBackController extends AbstractController
{
    #[Route(name: 'app_user_indexback', methods: ['GET'])]
    public function index(Request $request,UserRepository $userRepository, SessionInterface $session,EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $loggedInUserId = $session->get('user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $query = $userRepository->findAll();
        $pagination = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1), 
            4  
        );
        return $this->render('user/listemploye.html.twig', [
            'pagination' => $pagination,
            'loggedInUser' => $loggedInUser,
        ]);

    }

    #[Route('/listemployeback', name: 'app_user_listemploye', methods: ['GET'])]
    public function listemploye(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, SessionInterface $session, PaginatorInterface $paginator): Response
    {
        $loggedInUserId = $session->get('user_id');
    
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        if ($loggedInUser->getRole() !== 'agriculteur') {
            return $this->redirectToRoute('app_dashboard'); 
        }
    
        $roles = ['livreur', 'inspecteur', 'fermier'];
        $paginationlivreur = null;
        $paginationinspecteur = null;
        $paginationfermier = null;
    
        foreach ($roles as $role) {
            $query = $userRepository->createQueryBuilder('u')
                ->where('u.role = :role')
                ->andWhere('u.agriculteur = :agriculteur') 
                ->setParameter('role', $role)
                ->setParameter('agriculteur', $loggedInUser)
                ->getQuery();
    
            $pagination = $paginator->paginate(
                $query,
                $request->query->getInt('page_' . $role, 1),
                3
            );
    
            if ($role == 'livreur') {
                $paginationlivreur = $pagination;
            } elseif ($role == 'inspecteur') {
                $paginationinspecteur = $pagination;
            } elseif ($role == 'fermier') {
                $paginationfermier = $pagination;
            }
        }
    
        return $this->render('user/listemploye.html.twig', [
            'paginationlivreur' => $paginationlivreur,
            'paginationinspecteur' => $paginationinspecteur,
            'paginationfermier' => $paginationfermier,
            'loggedInUser' => $loggedInUser,
        ]);
    }

    
    

    #[Route('/listclientback', name: 'app_user_listclient', methods: ['GET'])]
public function listclient(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager, SessionInterface $session, PaginatorInterface $paginator): Response
{
    $loggedInUserId = $session->get('user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_loginback');
    }

    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);

    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_loginback');
    }
    if ($loggedInUser->getRole() !== 'agriculteur') {
        return $this->redirectToRoute('app_dashboard'); 
    }

    $query = $userRepository->createQueryBuilder('u')
        ->where('u.role = :role') 
        ->setParameter('role', 'client')  
        ->getQuery();

    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1), 
        4  
    );

    return $this->render('user/listclient.html.twig', [
        'pagination' => $pagination,
        'loggedInUser' => $loggedInUser, 
    ]);
}

    #[Route('/loginback', name: 'app_user_loginback', methods: ['GET', 'POST'])]
    public function loginback(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $error = null;
        $allowedRoles = ['agriculteur', 'inspecteur'];

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user || !password_verify($password, $user->getPassword())) {
                $error = 'Invalid email or password';
            } else {
                if (!in_array($user->getRole(), $allowedRoles)) {
                    $error = 'Accès refusé. Seuls les utilisateurs avec les rôles suivants peuvent se connecter : ' . implode(', ', $allowedRoles) . '.';
                } else {
                    $session->set('user_id', $user->getId());

                    User::setCurrentUser($user);

                    return $this->redirectToRoute('app_dashboard');
                }
            }
        }

        return $this->render('user/loginback.html.twig', [
            'error' => $error,
        ]);
    }

    #[Route('/logoutback', name: 'app_user_logoutback')]
    public function logoutback(SessionInterface $session): Response
    {
        $session->clear();
        return $this->redirectToRoute('app_user_loginback');
    }

    #[Route('/newback', name: 'app_user_newback', methods: ['GET', 'POST'])]
    public function newback(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
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
            $user->setRole('agriculteur');

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_loginback', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/newback.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    
    #[Route('/{id}/editback', name: 'app_user_editback', methods: ['GET', 'POST'])]
    public function editback(Request $request, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher, SessionInterface $session, int $id): Response {
        $loggedInUserId = $session->get('user_id');
    
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $user = $entityManager->getRepository(User::class)->find($id);
    
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }
    
        if ($loggedInUser->getRole() !== 'agriculteur' && $loggedInUser->getId() !== $user->getId()) {
            return $this->redirectToRoute('app_user_logoutback');
        }
    
        $form = $this->createForm(UserAdminEditType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('photo')->getData();
    
            if ($photoFile instanceof UploadedFile) {
                $uploadsDirectory = $this->getParameter('uploads_directory'); 
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move($uploadsDirectory, $newFilename);
                $user->setPhoto($newFilename);
            }
    
            $newPassword = $form->get('mdp')->getData();
            if ($newPassword) {
                $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
                $user->setPassword($hashedPassword);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_user_listemploye');
        }
    
        return $this->render('user/editback.html.twig', [
            'user' => $user, 
            'loggedInUser' => $loggedInUser, 
            'form' => $form,
        ]);
    }
    
    #[Route('/deleteemploye/{id}', name: 'app_user_deletebackemploye', methods: ['POST'])]
    public function deletebackemploye(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_listemploye', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/deleteclient/{id}', name: 'app_user_deletebackclient', methods: ['POST'])]
    public function deletebackclient(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_listclient', [], Response::HTTP_SEE_OTHER);
    }

    

    /*#[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }*/
    #[Route('/newemploye', name: 'app_user_newemploye', methods: ['GET', 'POST'])] // Removed {agriculteurId}
    public function newemploye(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, SessionInterface $session): Response
    {
        $loggedInUserId = $session->get('user_id');
    
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $user = new User();
        $form = $this->createForm(UserAdminType::class, $user);
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
            $user->setAgriculteur($loggedInUser);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_listemploye', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/add_employe.html.twig', [
            'user' => $user,
            'form' => $form,
            'loggedInUser' => $loggedInUser,
        ]);
}

    #[Route('/{id}/editprofiladmin', name: 'app_user_editprofiladmin', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher, SessionInterface $session): Response
    {
        $loggedInUserId = $session->get('user_id');
    
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
    
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $form = $this->createForm(UserEditType::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Handle Photo Upload
            $photoFile = $form->get('photo')->getData();
            if ($photoFile instanceof UploadedFile) {
                $uploadsDirectory = $this->getParameter('uploads_directory'); 
                $newFilename = uniqid().'.'.$photoFile->guessExtension();
                $photoFile->move($uploadsDirectory, $newFilename);
                $user->setPhoto($newFilename);
            }
    
            // Handle Password Change
            $plainPassword = $form->get('mdp')->getData();
            if (!empty($plainPassword)) { 
                $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);
                $user->setPassword($hashedPassword);
            }
    
            $entityManager->flush();
    
            return $this->redirectToRoute('app_dashboard', [], Response::HTTP_SEE_OTHER);
        }
    
        return $this->render('user/editprofiladmin.html.twig', [
            'user' => $user,
            'form' => $form,
            'loggedInUser' => $loggedInUser,
        ]);
    }
    


    #[Route('/ban/{id}', name: 'admin_ban_user')]
    public function banUser(int $id, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('danger', 'Utilisateur non trouvé.');
            return $this->redirectToRoute('app_user_listclient');
        }

        if ($user->getStatut() === 'banni') {
            $this->addFlash('info', 'Cet utilisateur est déjà banni.');
            return $this->redirectToRoute('app_user_listclient');
        }

        $user->setStatut('banni');
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur banni avec succès.');

        if ($session->get('user_id') == $user->getId()) {
            $session->remove('user_id');
            return $this->redirectToRoute('app_dashboard');
        }

        return $this->redirectToRoute('app_user_listclient');
    }
}