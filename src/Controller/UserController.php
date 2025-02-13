<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\UserType;
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

#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route(name: 'app_user_index', methods: ['GET'])]
    public function index(Request $request,UserRepository $userRepository, SessionInterface $session, PaginatorInterface $paginator): Response
    {
       /* if (!$session->get('user')) {
            return $this->redirectToRoute('app_user_login');
        }*/
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

    #[Route('/listemployeback', name: 'app_user_listemploye', methods: ['GET'])]
    public function listemploye(Request $request, UserRepository $userRepository, SessionInterface $session, PaginatorInterface $paginator): Response
    {  
        $user = $session->get('user');
    
        // Fetch only users with specific roles
        $query = $userRepository->createQueryBuilder('u')
            ->where('u.role IN (:roles)')
            ->setParameter('roles', ['livreur', 'inspecteur', 'fermier'])
            ->getQuery();
    
        $pagination = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1), 
            4  
        );
    
        return $this->render('user/listemploye.html.twig', [
            'pagination' => $pagination,
            'user' => $user,
        ]);
    }

    #[Route('/listclientback', name: 'app_user_listclient', methods: ['GET'])]
    public function listclient(Request $request, UserRepository $userRepository, SessionInterface $session, PaginatorInterface $paginator): Response
    {  
        $user = $session->get('user');
    
        $query = $userRepository->createQueryBuilder('u')
            ->where('u.role IN (:roles)')
            ->setParameter('roles', ['client'])
            ->getQuery();
    
        $pagination = $paginator->paginate(
            $query, 
            $request->query->getInt('page', 1), 
            4  
        );
    
        return $this->render('user/index.html.twig', [
            'pagination' => $pagination,
            'user' => $user,
        ]);
    }
    
    

    #[Route('/login', name: 'app_user_login', methods: ['GET', 'POST'])]
public function login(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
{
    if ($session->get('user')) {
        return $this->redirectToRoute('app_user_index');
    }

    $error = null;

    if ($request->isMethod('POST')) {
        $email = $request->request->get('email');
        $password = $request->request->get('password');

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

        if (!$user || !password_verify($password, $user->getPassword())) {
            $error = 'Invalid email or password';
        } else {
            $session->set('user', $user);

            return $this->redirectToRoute('role_interface', ['role' => $user->getRole()]);
        }
    }

    return $this->render('user/login.html.twig', [
        'error' => $error,
    ]);
}


    #[Route('/loginback', name: 'app_user_loginback', methods: ['GET', 'POST'])]
    public function loginback(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
       /* if ($session->get('user')) {
            return $this->redirectToRoute('app_user_index');
        }
*/
        $error = null;

        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $password = $request->request->get('password');

            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);

            if (!$user || !password_verify($password, $user->getPassword())) {
                $error = 'Invalid email or password';
            } else {
                if ($user->getRole() !== 'agriculteur') {
                    $error = 'Accès refusé. Seuls les agriculteurs peuvent se connecter.';
                } else {
                    $session->set('user', $user);
                    return $this->redirectToRoute('app_dashboard');
                }
            }
        }

        return $this->render('user/loginback.html.twig', [
            'error' => $error,
        ]);
    }


    #[Route('/logout', name: 'app_user_logout')]
    public function logout(SessionInterface $session): Response
    {
        $session->clear();
        return $this->redirectToRoute('app_home');
    }

    #[Route('/logoutback', name: 'app_user_logoutback')]
    public function logoutback(SessionInterface $session): Response
    {
        $session->clear();
        return $this->redirectToRoute('app_user_loginback');
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
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

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
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

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
    {
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
            $entityManager->flush();

            return $this->redirectToRoute('user_profile', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/editback', name: 'app_user_editback', methods: ['GET', 'POST'])]
    public function editback(Request $request, User $user, EntityManagerInterface $entityManager,UserPasswordHasherInterface $passwordHasher): Response
    {
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
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/editback.html.twig', [
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

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }



}
