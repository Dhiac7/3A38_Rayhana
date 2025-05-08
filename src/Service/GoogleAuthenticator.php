<?php
namespace App\Service;

use KnpU\OAuth2ClientBundle\Client\Provider\GoogleClient;
use League\OAuth2\Client\Provider\GoogleUser;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class GoogleAuthenticator extends AbstractAuthenticator
{
    private GoogleClient $client;
    private RouterInterface $router;
    private EntityManagerInterface $entityManager;
    private UserRepository $userRepository;
    private SluggerInterface $slugger;
    private string $uploadsDirectory;

    public function __construct(
        GoogleClient $client, 
        RouterInterface $router, 
        EntityManagerInterface $entityManager,
        UserRepository $userRepository,
        SluggerInterface $slugger,
        ParameterBagInterface $params
    ) {
        $this->client = $client;
        $this->router = $router;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
        $this->slugger = $slugger;
        $this->uploadsDirectory = $params->get('uploads_directory');
    }

    public function supports(Request $request): ?bool
    {
        return $request->attributes->get('_route') === 'connect_google_check';
    }

    public function authenticate(Request $request): Passport
    {
        $accessToken = $this->client->getAccessToken();
        $googleUser = $this->client->fetchUserFromToken($accessToken);
        $email = $googleUser->getEmail();
    
        return new SelfValidatingPassport(
            new UserBadge($email, function () use ($googleUser) {
                $user = $this->userRepository->findOneBy(['email' => $googleUser->getEmail()]);
    
                if (!$user) {
                    $user = new User();
                    $user->setGoogleId($googleUser->getId());
                    $user->setEmail($googleUser->getEmail());
    
                    $fullName = $googleUser->getName();
                    $nameParts = explode(' ', $fullName, 2); 
                    $prenom = $nameParts[0] ?? ''; 
                    $nom = $nameParts[1] ?? ''; 
    
                    $user->setNom($nom);
                    $user->setPrenom($prenom);
    
                    $cin = str_pad(mt_rand(0, 99999999), 8, '0', STR_PAD_LEFT); 
                    $user->setCin($cin);
    
                    $randomPassword = bin2hex(random_bytes(8)); 
                    //$hashedPassword = $this->passwordHasher->hashPassword($user, $randomPassword);
                    $user->setMdp($randomPassword);
    
                    $user->setTel('00000000');
    
                    $user->setRole('client');
    
                    $user->setStatut('actif');
    
                    $user->setGenre('autre');
    
                    $user->setAnneeNaissance(1990);
    
                    $slug = $this->slugger->slug($fullName)->lower();
                    $user->setSlug($slug);
    
                    $user->setCreatedAt(new \DateTime());
    
                    $avatar = $googleUser->getAvatar();
                    if ($avatar) {
                        $newFilename = uniqid() . '.jpg';
                        $imageData = file_get_contents($avatar);
                        file_put_contents($this->uploadsDirectory . '/' . $newFilename, $imageData);
                        $user->setPhoto($newFilename);
                    }
    
                    $this->entityManager->persist($user);
                    $this->entityManager->flush();
                    User::setCurrentUser($user);
                    $user->setStatut('actif');
                }
    
                return $user;
            })
        );
    }
    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): ?RedirectResponse
    {
        $user = $token->getUser();

        $request->getSession()->set('user_id', $user->getId());

        return new RedirectResponse($this->router->generate('role_interface', ['role' => $user->getRole()]));
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?RedirectResponse
    {
       // $request->getSession()->getFlashBag()->add('error', 'Google authentication failed. Please try again.');

        return new RedirectResponse($this->router->generate('app_user_login'));
    }
}