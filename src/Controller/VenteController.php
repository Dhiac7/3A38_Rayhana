<?php

namespace App\Controller;

use App\Entity\User;

use App\Entity\Vente;
use App\Entity\Produit;
use Stripe\Stripe;
use App\Entity\Transactionfinancier;
use App\Entity\Atelier;
use App\Service\FaceRecognitionService; 
use App\Form\VenteType;
use App\Repository\VenteRepository;
use App\Repository\ProduitRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Endroid\QrCode\Builder\BuilderInterface;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\Writer\PngWriter;


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
        EntityManagerInterface $entityManager,
        BuilderInterface $qrCodeBuilder
    ): Response {
        $loggedInUserId = $session->get('user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }

        // Récupérer l'utilisateur connecté
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_login');
        }

        // Récupérer les ventes pour l'utilisateur connecté
        $query = $venteRepository->createQueryBuilder('v')
            ->where('v.user = :user')
            ->setParameter('user', $loggedInUser)
            ->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            6
        );

        // Générer un QR Code pour chaque vente
        $qrCodes = [];
        foreach ($pagination as $vente) {
            $qrCode = $qrCodeBuilder
                ->size(200)
                ->encoding(new Encoding('UTF-8'))
                ->writer(new PngWriter())
                ->data("Vente ID: {$vente->getId()} - Prix: {$vente->getPrix()}€ - Méthode de paiement: {$vente->getMethodePayement()} - Date: {$vente->getDate()->format('Y-m-d H:i:s')}")


                ->labelText("Vente {$vente->getId()}") // ✅ Remplace `label()` par `labelText()`
                ->labelFont(new NotoSans(10)) // ✅ Utilise `labelFont()` pour définir la police
                ->build();

            // Convertir en base64 pour l'affichage dans Twig
            $qrCodes[$vente->getId()] = $qrCode->getDataUri();
        }

        return $this->render('vente/index.html.twig', [
            'pagination' => $pagination,
            'loggedInUser' => $loggedInUser,
            'qrCodes' => $qrCodes,
        ]);
    }


    #[Route('/new', name: 'app_vente_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        ProduitRepository $produitRepository,
        SessionInterface $session,
        FaceRecognitionService $faceRecognitionService
    ): Response {
        $loggedInUserId = $session->get('user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_login');
        }

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

        // Vérifier si le produit est en stock
        if ($produit->getQuantite() <= 0) {
            $this->addFlash('error', 'Stock insuffisant pour ce produit.');
            return $this->redirectToRoute('app_vente_index');
        }

        $vente->setNom($produit->getNom());
        $vente->setProduit($produit);
        $vente->setPrix($produit->getPrixVente());
        $vente->setQuantite(1);
        $vente->setUser($loggedInUser);

        $form = $this->createForm(VenteType::class, $vente, [
            'prix_unitaire' => $produit->getPrixVente(),
        ]);
        $form->handleRequest($request);

        // Configurer la clé API Stripe
        Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        if ($form->isSubmitted() && $form->isValid()) {
            $paymentMethod = $form->get('methodepayement')->getData();

            if ($paymentMethod === 'carte_bancaire') {
                // Récupérer le token Stripe
                $token = $request->request->get('stripeToken');

                if (!$token) {
                    $this->addFlash('error', 'Le token de paiement est manquant.');
                    return $this->redirectToRoute('app_vente_new', ['id' => $produitId]);
                }

                try {
                    $stripeClient = new StripeClient($this->getParameter('stripe_secret_key'));
                    $charge = $stripeClient->charges->create([
                        'amount' => $vente->getPrix() * 100, // Montant en cents
                        'currency' => 'eur', // Devise
                        'source' => $token, // Token Stripe
                        'description' => 'Achat de ' . $produit->getNom(),
                    ]);

                    // Vérifiez si le paiement a réussi
                    if ($charge->status !== 'succeeded') {
                        throw new \Exception('Le paiement a échoué.');
                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Erreur lors du traitement du paiement: ' . $e->getMessage());
                    return $this->redirectToRoute('app_vente_new', ['id' => $produitId]);
                }
            } else {
                if ($paymentMethod !== 'carte_bancaire') {
                    $userImage = $loggedInUser->getPhoto();
                    if (!$faceRecognitionService->verifyFace($userImage)) {
                        $this->addFlash('error', 'Reconnaissance faciale échouée.');
                        return $this->redirectToRoute('app_vente_index');
                    }
                }
            }

            // Réduire la quantité du produit
            if ($produit->getQuantite() < $vente->getQuantite()) {
                $this->addFlash('error', 'Stock insuffisant pour cette quantité.');
                return $this->redirectToRoute('app_vente_new', ['id' => $produitId]);
            }

            $produit->setQuantite($produit->getQuantite() - $vente->getQuantite());

            // Créer la transaction financière
            $transaction = new Transactionfinancier();
            $transaction->setMontant($vente->getPrix());
            $transaction->setDate(new \DateTime());
            $transaction->setType('Revenue'); // Définir le type de la transaction
            $transaction->setVente($vente); // Associer la transaction à la vente

            // Associer la transaction à la vente
            $vente->setTransaction($transaction);

            // Persister la vente, la transaction, et le produit mis à jour
            $entityManager->persist($vente);
            $entityManager->persist($produit); // Mettre à jour le produit
            $entityManager->persist($transaction); // Persister la transaction
            $entityManager->flush();

            $this->addFlash('success', 'Vente validée avec succès.');
            return $this->redirectToRoute('app_vente_index');
        }

        return $this->render('vente/new.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
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

    // Route pour afficher une vente spécifique (avec contrainte sur l'ID)
    #[Route('/{id}', name: 'app_vente_show', methods: ['GET'], requirements: ['id' => '\d+'])]
    public function show(Vente $vente, SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $loggedInUserId = $session->get('user_id');
        
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
    public function edit(Request $request, Vente $vente, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $loggedInUserId = $session->get('user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        $form = $this->createForm(VenteType::class, $vente, [
            'prix_unitaire' => $vente->getProduit()->getPrixVente(),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
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

    // Route pour afficher la création d'une vente d'atelier
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

    // Récupération des paramètres depuis la requête
    $nom = $request->query->get('nom');
    $prix = $request->query->get('prix');
    $dateAtelier = $request->query->get('dateAtelier');

    // Récupérer l'atelier depuis la base de données
    $atelier = $entityManager->getRepository(Atelier::class)->findOneBy([
        'nom' => $nom,
        'prix' => $prix,
        'date_atelier' => new \DateTime($dateAtelier) // Assurez-vous que le champ est correct
    ]);

    if (!$atelier) {
        // Gérer le cas où l'atelier n'existe pas
        $this->addFlash('error', 'Atelier non trouvé.');
        return $this->redirectToRoute('app_vente_index');
    }

    // Vérification des places disponibles dans l'atelier
    if ($atelier->getNbrplacedispo() === null) {
        $atelier->setNbrplacedispo(50); // Valeur par défaut de 50 places
    }

    // Création de la vente
    $vente = new Vente();
    $vente->setNom($atelier->getNom());
    $vente->setPrix($atelier->getPrix());
    $vente->setQuantite(1); // Valeur par défaut pour le formulaire
    $vente->setUser($loggedInUser);

    // Créer et gérer le formulaire de vente
    $form = $this->createForm(VenteType::class, $vente, [
        'prix_unitaire' => $atelier->getPrix(),
    ]);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $quantite = $vente->getQuantite();
        $prixUnitaire = $atelier->getPrix();
        $vente->setPrix($quantite * $prixUnitaire);

        // Vérification si l'atelier a suffisamment de places disponibles
        if ($atelier->getNbrplacedispo() < $quantite) {
            $this->addFlash('error', 'Pas assez de places disponibles pour cet atelier.');
            return $this->redirectToRoute('app_vente_atelier_new', [
                'nom' => $nom,
                'prix' => $prix,
                'dateAtelier' => $dateAtelier
            ]);
        }

        // Réduire le nombre de places disponibles dans l'atelier
        $atelier->setNbrplacedispo($atelier->getNbrplacedispo() - $quantite);

        // Créer la transaction financière
        $transaction = new Transactionfinancier();
        $transaction->setMontant($vente->getPrix());
        $transaction->setDate(new \DateTime());
        $transaction->setType('Revenue');
        $transaction->setVente($vente);

        // Associer la transaction à la vente
        $vente->setTransaction($transaction);

        // Sauvegarder l'entité Vente, Transaction et Atelier
        $entityManager->persist($vente);
        $entityManager->persist($transaction);
        $entityManager->persist($atelier); // Mettre à jour l'atelier avec le nombre de places restantes
        $entityManager->flush();

        // Message de succès
        $this->addFlash('success', 'Vente et transaction enregistrées avec succès.');
        return $this->redirectToRoute('app_vente_index');
    }

    // Rendu du template avec les données
    return $this->render('vente/newatelier.html.twig', [
        'form' => $form->createView(),
        'loggedInUser' => $loggedInUser,
        'atelier' => $atelier,  // Passer l'atelier à la vue
    ]);
}
}




