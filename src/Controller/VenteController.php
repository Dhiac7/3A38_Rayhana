<?php

namespace App\Controller;
use App\Entity\User;
use App\Entity\Vente;
use App\Entity\Produit;
use App\Form\VenteType;
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
use App\Form\AtelierType; // Importez correctement le formulaire
use App\Entity\Atelier; // Importez l'entité Atelier

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
    $loggedInUserId = $session->get('client_user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_login');
    }

    // Récupérer l'utilisateur connecté
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_login');
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
        $loggedInUserId = $session->get('admin_user_id');
        
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

    // Route pour créer une nouvelle vente (définie avant les routes dynamiques comme /{id})
    #[Route('/new', name: 'app_vente_new', methods: ['GET', 'POST'])]
public function new(
    Request $request,
    EntityManagerInterface $entityManager,
    ProduitRepository $produitRepository,
    SessionInterface $session
): Response {
    $loggedInUserId = $session->get('client_user_id');
    
    if (!$loggedInUserId) {
        return $this->redirectToRoute('app_user_login');
    }

    // Récupérer l'utilisateur connecté
    $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
    if (!$loggedInUser) {
        return $this->redirectToRoute('app_user_login');
    }

    $vente = new Vente();
    
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
    
    // Définir le nom de la vente avec le nom du produit
    $vente->setNom($produit->getNom());
    
    $vente->setProduit($produit);
    $vente->setPrix($produit->getPrixVente());
    $vente->setQuantite(1);

    // Associer l'utilisateur connecté à la vente
    $vente->setUser($loggedInUser);

    $form = $this->createForm(VenteType::class, $vente, [
        'prix_unitaire' => $produit->getPrixVente(),
    ]);
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $quantite = $vente->getQuantite();
        $prixUnitaire = $produit->getPrixVente();
        $vente->setPrix($quantite * $prixUnitaire);
        
        // Créer une nouvelle transaction financière
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
    
    return $this->render('vente/new.html.twig', [
        'form' => $form->createView(),
        'produit' => $produit,
        'loggedInUser' => $loggedInUser,
    ]);
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



    #[Route('/atelier/new', name: 'app_vente_atelier_new', methods: ['GET', 'POST'])]
public function newVenteAtelier(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
{
    $loggedInUserId = $session->get('client_user_id');
    
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

    $form = $this->createForm(VenteType::class, $vente);
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