<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Vente;
use App\Entity\Produit;
use Psr\Log\LoggerInterface;
use App\Form\VenteType;
use Stripe\Stripe;
use App\Service\PredictionService;
use Stripe\Checkout\Session as StripeSession;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Transactionfinancier;
use App\Repository\VenteRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Form\AtelierType; 
use App\Entity\Atelier; 

#[Route('/vente')]
final class VenteController extends AbstractController
{
    // Route pour l'index
    #[Route('/', name: 'app_vente_index', methods: ['GET'])]
public function index(
    Request $request,
    VenteRepository $venteRepository,
    PaginatorInterface $paginator,
    SessionInterface $session,
    EntityManagerInterface $entityManager
): Response {
    $loggedInUserId = $session->get('user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_login');
    }

    // Récupérer l'utilisateur connecté
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('user_login');
    }

    // Filtrer les ventes pour l'utilisateur connecté
    $query = $venteRepository->createQueryBuilder('v')
        ->where('v.user = :user')
        ->setParameter('user', $loggedInUser)
        ->getQuery();

    $pagination = $paginator->paginate(
        $query,
        $request->query->getInt('page', 1),
        6
    );

    return $this->render('vente/index.html.twig', [
        'pagination' => $pagination,
        'loggedInUser' => $loggedInUser,
    ]);
}

    #[Route('/indexback', name: 'app_vente_indexback', methods: ['GET'])]
    public function indexback(Request $request, VenteRepository $venteRepository, PaginatorInterface $paginator,SessionInterface $session,EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $query = $venteRepository->findAll();
        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6
        );

       return $this->render('vente/indexback.html.twig', [
    'pagination' => $pagination,
    'loggedInUser' => $loggedInUser,
]);

    }


   

    #[Route('/new', name: 'app_vente_new', methods: ['GET', 'POST'])]
public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    ProduitRepository $produitRepository,
    SessionInterface $session,
    PredictionService $predictionService
): Response {
    // Vérification de l'utilisateur connecté
    $loggedInUserId = $session->get('user_id');
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_login');
    }
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_login');
    }

    // Récupération du produit
    $produitId = $request->query->get('id');
    if (!$produitId) {
        $this->addFlash('error', 'Aucun produit sélectionné.');
        return $this->redirectToRoute('app_vente_index');
    }
    $produit = $produitRepository->find($produitId);
    if (!$produit) {
        $this->addFlash('error', 'Produit introuvable.');
        return $this->redirectToRoute('app_vente_index');
    }

    // Calcul des paramètres de date pour la prédiction
    $currentDate = new \DateTime();
    $jourSemaine = (int)$currentDate->format('w'); // 0 (dimanche) à 6 (samedi)
    $mois = (int)$currentDate->format('n'); // 1 à 12

    // Appel au service de prédiction
    try {
        $predictionData = [
            'nom' => $produit->getNom(),
            'prix' => $produit->getPrixVente(),
            'jour_semaine' => $jourSemaine === 0 ? 6 : $jourSemaine - 1, // Ajustement pour Python
            'mois' => $mois
        ];
        $prediction = $predictionService->predict($predictionData);
    } catch (\Exception $e) {
        $prediction = null;
        $this->addFlash('warning', 'Service de prédiction temporairement indisponible');
    }

    // Initialisation de la vente
    $vente = new Vente();
    $vente->setProduit($produit);
    $vente->setNom($produit->getNom());
    $vente->setPrix($produit->getPrixVente());
    $vente->setQuantite(1);
    $vente->setUser($loggedInUser);

    // Création du formulaire
    $form = $this->createForm(VenteType::class, $vente, [
        'prix_unitaire' => $produit->getPrixVente(),
    ]);
    $form->handleRequest($request);

    // Traitement du formulaire
    if ($form->isSubmitted() && $form->isValid()) {
        // Calcul du prix total
        $quantite = $vente->getQuantite();
        $prixUnitaire = $produit->getPrixVente();
        $vente->setPrix($quantite * $prixUnitaire);

        // Gestion du paiement
        $paymentMethod = $form->get('methodepayement')->getData();
        
        if ($paymentMethod === 'carte_bancaire') {
            // Logique Stripe
        } else {
            // Logique de transaction standard
        }

        return $this->redirectToRoute('app_vente_index');
    }

    return $this->render('vente/new.html.twig', [
        'form' => $form->createView(),
        'produit' => $produit,
        'loggedInUser' => $loggedInUser,
        'prediction' => $prediction,
    ]);
}
    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(Request $request): Response
    {
        // Mettez à jour le statut de la vente si besoin
        $this->addFlash('success', 'Paiement effectué avec succès.');
        return $this->redirectToRoute('app_vente_index');
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        $this->addFlash('error', 'Paiement annulé.');
        return $this->redirectToRoute('app_vente_index');
    }



    // Route pour afficher une vente spécifique (avec contrainte sur l'ID)
    #[Route('/{id}', name: 'app_vente_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Vente $vente, SessionInterface $session,EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('admin_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
        return $this->render('vente/show.html.twig', [
            'vente' => $vente,
           'loggedInUser' => $loggedInUser,
        ]);
    }

    // Route pour éditer une vente
    #[Route('/{id}/edit', name: 'app_vente_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Vente $vente, EntityManagerInterface $entityManager,SessionInterface $session,): Response
{
    $loggedInUserId = $session->get('admin_user_id');
        
        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }
    $form = $this->createForm(VenteType::class, $vente, [
        'prix_unitaire' => $vente->getProduit()->getPrixVente(), // Passer le prix unitaire au formulaire
    ]);

    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Calculer le prix total
        $quantite = $vente->getQuantite();
        $prixUnitaire = $vente->getProduit()->getPrixVente();
        $vente->setPrix($quantite * $prixUnitaire);

        $entityManager->flush();

        $this->addFlash('success', 'Vente mise à jour avec succès.');
        return $this->redirectToRoute('app_vente_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('vente/edit.html.twig', [
        'vente' => $vente,
        'form' => $form->createView(),
        'loggedInUser' => $loggedInUser,
    ]);
}

    // Route pour supprimer une vente
    #[Route('/{id}', name: 'app_vente_delete', methods: ['POST'])]
    public function delete(Request $request, Vente $vente, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vente->getId(), $request->request->get('_token'))) {
            $entityManager->remove($vente);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_vente_index', [], Response::HTTP_SEE_OTHER);
    }



   // src/Controller/VenteController.php

#[Route('/atelier/new', name: 'app_vente_atelier_new', methods: ['GET', 'POST'])]
public function newVenteAtelier(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
{
    $loggedInUserId = $session->get('user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_login');
    }

    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_login');
    }

    $nom = $request->query->get('nom');
    $prix = $request->query->get('prix');
    $dateAtelier = $request->query->get('dateAtelier');
    
    // Créez une nouvelle vente avec les informations de l'atelier
    $vente = new Vente();
    $atelier = new Atelier();
    $atelier->setNom($nom);
    $atelier->setPrix($prix);
    $atelier->setDateAtelier(new \DateTime($dateAtelier));

    // Associer l'atelier à la vente
    $vente->setNom($atelier->getNom());
    $vente->setPrix($atelier->getPrix());
    $vente->setQuantite(1); // Par défaut, mettre une quantité de 1

    // Associer l'utilisateur connecté à la vente
    $vente->setUser($loggedInUser);

    $form = $this->createForm(VenteType::class, $vente, [
        'prix_unitaire' => $atelier->getPrix(), // Passer le prix unitaire au formulaire
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Calcul du prix total en fonction de la quantité
        $quantite = $vente->getQuantite();
        $prixUnitaire = $atelier->getPrix();
        $vente->setPrix($quantite * $prixUnitaire);

        // Créer une nouvelle transaction
        $transaction = new Transactionfinancier();
        $transaction->setMontant($vente->getPrix());
        $transaction->setDate(new \DateTime());
        $transaction->setType('Revenue'); // Type de transaction
        $transaction->setVente($vente); // Associer la transaction à la vente

        // Associer la transaction à la vente
        $vente->setTransaction($transaction);

        // Persister la vente et la transaction
        $entityManager->persist($vente);
        $entityManager->persist($transaction);
        $entityManager->flush();

        $this->addFlash('success', 'Vente et transaction enregistrées avec succès.');
        return $this->redirectToRoute('app_vente_index');
    }

    return $this->render('vente/newatelier.html.twig', [
        'form' => $form->createView(),
        'atelier' => $atelier, // Passer l'atelier à la vue si nécessaire
    ]);
}
}