<?php

// src/Controller/ChatbotController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class ChatbotController extends AbstractController
{
    #[Route('/chatbot', name: 'chatbot', methods: ['POST'])]
    public function chatbot(Request $request): JsonResponse
    {
        $message = $request->request->get('message');
        $response = '';

        // Liste des questions et réponses
        $qaList = [
            // 1-20: Questions Générales
            'Qu’est-ce que l’agriculture biologique ?' => 'L’agriculture biologique utilise des méthodes naturelles sans produits chimiques pour cultiver des plantes et élever des animaux.',
            'Quels sont les types de cultures les plus rentables ?' => 'Les cultures comme la tomate 🍅, le blé 🌾, l’olive 🫒 et l’amande sont souvent rentables selon la région.',
            'Comment améliorer la productivité des cultures ?' => 'Utilisez des semences certifiées, l’irrigation goutte-à-goutte et la rotation des cultures.',
            'Quel est le meilleur engrais naturel ?' => 'Le compost, le fumier de vache et le fumier de volaille sont d’excellents engrais organiques.',
            'Comment gérer les mauvaises herbes ?' => 'Utilisez la technique de paillage, la rotation des cultures et le désherbage manuel.',
            
            // 21-40: Irrigation
            'Quels sont les types d’irrigation utilisés en agriculture ?' => 'Goutte-à-goutte, irrigation par aspersion, et irrigation de surface.',
            'Comment économiser l’eau lors de l’irrigation ?' => 'Utilisez l’irrigation goutte-à-goutte et récoltez l’eau de pluie.',
            'Quels sont les avantages de l’irrigation goutte-à-goutte ?' => 'Économie d’eau, réduction des maladies, et meilleur contrôle de l’humidité.',
            'Quelle est la meilleure heure pour irriguer ?' => 'Tôt le matin ou en fin d’après-midi pour éviter l’évaporation.',
            
            // 41-60: Fertilisation
            'Quel engrais utiliser pour les tomates ?' => 'Utilisez des engrais riches en potassium et en phosphore.',
            'Quand faut-il appliquer le compost ?' => 'Avant la plantation et pendant la croissance des plantes.',
            'Quels sont les signes de carence en azote dans les plantes ?' => 'Feuilles jaunies et croissance lente.',
            'Quels sont les meilleurs fertilisants biologiques ?' => 'Compost, fumier et algues marines.',
            
            // 61-80: Lutte contre les ravageurs
            'Comment lutter naturellement contre les pucerons ?' => 'Utilisez du savon noir ou des coccinelles 🐞.',
            'Quels insectes sont bénéfiques pour les cultures ?' => 'Les coccinelles, les abeilles et les vers de terre.',
            'Qu’est-ce que la lutte biologique ?' => 'Utilisation d’insectes ou de micro-organismes pour contrôler les ravageurs.',
            
            // 81-100: Gestion des sols
            'Comment améliorer la fertilité du sol ?' => 'Ajoutez du compost et pratiquez la rotation des cultures.',
            'Quels sont les tests pour vérifier la qualité du sol ?' => 'Test de pH, test de texture et test de matière organique.',
            
            // 101-120: Agriculture durable
            'Qu’est-ce que l’agriculture de conservation ?' => 'Techniques visant à préserver les sols et à économiser l’eau.',
            'Comment réduire l’utilisation de pesticides ?' => 'Utilisez la lutte biologique et des répulsifs naturels.',
            
            // 121-140: Machines agricoles
            'Quels sont les outils agricoles indispensables ?' => 'Tracteurs, semoirs, pulvérisateurs et moissonneuses.',
            'Comment entretenir une moissonneuse ?' => 'Nettoyez les filtres, vérifiez les niveaux d’huile et graissez les pièces mobiles.',
            
            // 141-160: Ventes & commercialisation
            'Comment vendre ses produits agricoles ?' => 'Marchés locaux, coopératives et ventes en ligne.',
            'Quels sont les avantages de la vente directe ?' => 'Prix plus élevés, relation directe avec le client.',
            
            // 161-180: Agriculture intelligente
            'Qu’est-ce que l’agriculture intelligente ?' => 'Utilisation des capteurs, drones et logiciels pour optimiser la production.',
            'Comment utiliser les drones en agriculture ?' => 'Surveillance des cultures, pulvérisation de pesticides et cartographie des sols.',
            
            // 181-200: Subventions & Financement
            'Comment obtenir des subventions agricoles ?' => 'Présentez une demande auprès du ministère de l’agriculture ou des ONG.',
            'Quels sont les documents nécessaires pour obtenir un crédit agricole ?' => 'Plan d’affaires, carte d’identité, titre foncier et devis des équipements.',
            
            // 201-220: Stockage des récoltes
            'Comment stocker les céréales sans pesticides ?' => 'Utilisez des silos hermétiques et des feuilles de neem.',
            'Quels sont les meilleurs sacs pour le stockage des céréales ?' => 'Sacs hermétiques en polyéthylène.',
            'Bonjour' => 'Bonjour 🌾! Comment puis-je vous aider ?',
            'Comment entretenir mes plantes ?' => 'Arrosez vos plantes régulièrement et utilisez des engrais naturels 🌿.',
            'Quel type de culture recommandez-vous ?' => 'Je recommande la culture de tomates 🍅, blé 🌾 et pommes de terre 🥔 en hiver.',
            'Qu’est-ce que l’agriculture biologique ?' => 'L’agriculture biologique est un système de production qui exclut l’usage de produits chimiques de synthèse, favorisant des pratiques respectueuses de l’environnement.',
            'Quels sont les avantages de la rotation des cultures ?' => 'La rotation des cultures aide à prévenir l’épuisement des sols, réduit les maladies et les parasites, et améliore la fertilité du sol.',
            'Comment puis-je améliorer la qualité de mon sol ?' => 'L’ajout de compost, la rotation des cultures et l’utilisation de cultures de couverture peuvent améliorer la qualité du sol.',
            'Quels sont les principaux défis de l’agriculture moderne ?' => 'Les défis incluent le changement climatique, la gestion durable des ressources, et la sécurité alimentaire.',
            'Qu’est-ce que l’agriculture de conservation ?' => 'C’est une approche qui vise à améliorer la durabilité en minimisant le travail du sol, en maintenant une couverture permanente du sol, et en diversifiant les cultures.',
            'Comment gérer efficacement l’eau dans l’agriculture ?' => 'L’irrigation efficace, la collecte des eaux de pluie, et l’utilisation de cultures résistantes à la sécheresse sont des stratégies clés.',
            'Quels sont les impacts du changement climatique sur l’agriculture ?' => 'Le changement climatique peut entraîner des conditions météorologiques extrêmes, affectant les rendements des cultures et la sécurité alimentaire.',
            'Qu’est-ce que la permaculture ?' => 'Un système agricole qui imite les écosystèmes naturels pour créer des cultures durables et résilientes 🌱.',
            'Comment faire du compost ?' => 'Mélangez des déchets verts (épluchures) et bruns (feuilles sèches), maintenez humide et retournez régulièrement ♻️.',
            'Quels sont les meilleurs légumes à cultiver pour un débutant ?' => 'Les radis, salades, courgettes et haricots verts sont faciles à cultiver pour les débutants 🥗.',
            'Comment lutter contre les pucerons naturellement ?' => 'Utilisez du savon noir dilué, des coccinelles ou une décoction d’ortie pour éliminer les pucerons 🐞.',
            'Qu’est-ce que l’agroforesterie ?' => 'C’est l’association d’arbres et de cultures ou d’élevage sur une même parcelle pour créer des synergies bénéfiques 🌳.',
            'Comment reconnaître un sol fertile ?' => 'Un sol fertile est riche en matière organique, bien drainé, aéré et contient de nombreux organismes vivants comme les vers de terre 🌱.',
            'Quelles sont les meilleures périodes pour planter des pommes de terre ?' => 'Plantez les pommes de terre entre mars et avril pour une récolte estivale 🥔.',
            'Comment utiliser le purin d’ortie ?' => 'Utilisez-le dilué à 10% comme engrais et à 20% comme insecticide naturel 🌿.',
            'Quels sont les avantages de l’agriculture urbaine ?' => 'Elle réduit les îlots de chaleur, améliore la sécurité alimentaire locale et favorise la biodiversité en ville 🏙️.',
            'Comment conserver les semences ?' => 'Stockez-les dans un endroit frais, sec et à l’abri de la lumière dans des contenants hermétiques 🌰.',
            'Qu’est-ce que la lutte biologique ?' => 'C’est l’utilisation d’organismes vivants pour contrôler les ravageurs et les maladies des cultures 🐛.',
            'Comment installer un système d’irrigation goutte-à-goutte ?' => 'Connectez un tuyau principal à une source d’eau, puis ajoutez des goutteurs près de chaque plante pour un arrosage économique 💧.',
            'Quels sont les critères d’un label bio ?' => 'Absence de produits chimiques de synthèse, respect du bien-être animal et de la biodiversité, traçabilité des produits 🌿.',
            'Comment prévenir l’érosion des sols ?' => 'Plantez des haies, utilisez des cultures de couverture et pratiquez le labour minimal pour protéger la structure du sol 🌎.',
            'Qu’est-ce que l’hydroponie ?' => 'C’est une méthode de culture hors-sol où les plantes poussent dans une solution nutritive sans terre 💧.',
            'Comment attirer les pollinisateurs dans mon jardin ?' => 'Plantez des fleurs riches en nectar comme la lavande, le tournesol ou le thym, et évitez les pesticides 🐝.',
            'Quels sont les avantages des semences paysannes ?' => 'Elles préservent la biodiversité, sont adaptées aux terroirs locaux et permettent l’autonomie des agriculteurs 🌱.',
            'Comment faire une butte en permaculture ?' => 'Superposez des couches de matière organique (bois, compost, paille) pour créer un sol fertile et auto-entretenu 🌄.',
            'Qu’est-ce que l’agriculture régénératrice ?' => 'Une approche qui vise à restaurer la santé des sols et des écosystèmes tout en produisant des aliments nutritifs 🌱.',
            'Comment tailler un arbre fruitier ?' => 'Éliminez les branches mortes, aérez le centre et conservez une forme équilibrée pendant la période de dormance ✂️.',
            'Quels sont les principes de l’agriculture biodynamique ?' => 'Elle considère la ferme comme un organisme vivant et utilise des préparations spécifiques selon les rythmes cosmiques 🌙.',
            'Comment choisir les variétés adaptées à mon climat ?' => 'Consultez les catalogues régionaux, observez ce qui pousse localement et privilégiez les variétés résistantes aux conditions de votre région 🌡️.',
            'Qu’est-ce que la méthode Fukuoka ?' => 'Une approche d’agriculture naturelle qui minimise l’intervention humaine et laisse agir les processus naturels 🍃.',
            'Comment planifier une rotation des cultures efficace ?' => 'Alternez les familles botaniques et les besoins nutritifs (légumineuses puis légumes-fruits puis légumes-feuilles) 🔄.',
            'Quels sont les avantages du paillage ?' => 'Il conserve l’humidité, limite les mauvaises herbes et enrichit progressivement le sol en se décomposant 🍂.',
            'Comment faire ses propres semences ?' => 'Sélectionnez les meilleures plantes, laissez-les monter en graines, récoltez à maturité et stockez dans un lieu sec 🌱.',
            'Qu’est-ce que la phytoremédiation ?' => 'L’utilisation de plantes pour dépolluer des sols contaminés par des métaux lourds ou d’autres polluants 🌿.',
            'Comment cultiver des champignons ?' => 'Utilisez un substrat adapté (paille, marc de café), ensemencez avec du mycélium et maintenez l’humidité 🍄.',
            'Quels sont les principes de la traction animale moderne ?' => 'Une alternative écologique aux tracteurs, utilisant des animaux formés avec des outils adaptés pour travailler le sol 🐎.',
            'Comment réaliser un diagnostic de sol ?' => 'Observez sa texture, sa structure, mesurez son pH et identifiez les plantes bio-indicatrices présentes 🔍.',
            'Qu’est-ce que l’agriculture verticale ?' => 'Un système de production en hauteur, souvent en intérieur, qui optimise l’espace en cultivant sur plusieurs niveaux 🏙️.',
            'Comment composter en appartement ?' => 'Utilisez un lombricomposteur qui transforme vos déchets organiques en compost grâce à l’action des vers 🪱.',
            'Quels sont les avantages des légumineuses dans la rotation ?' => 'Elles fixent l’azote atmosphérique dans le sol, enrichissant naturellement la terre pour les cultures suivantes 🌱.',
            'Comment lutter contre le mildiou de la tomate ?' => 'Évitez l’humidité sur le feuillage, espacez les plants et utilisez des préparations à base de prêle ou de bicarbonate 🍅.',
            'Qu’est-ce que l’agroécologie ?' => 'Une approche qui applique les principes écologiques à l’agriculture pour la rendre plus durable et résiliente 🌍.',
            'Comment réaliser une greffe d’arbre fruitier ?' => 'Assemblez un greffon (variété désirée) sur un porte-greffe compatible en assurant un bon contact des cambiums 🌳.',
            'Quels sont les principes de la biodynamie ?' => 'Elle considère l’exploitation comme un organisme vivant et utilise des préparations spécifiques selon le calendrier lunaire 🌕.',
            'Comment créer une haie biodiversifiée ?' => 'Mélangez des espèces locales à floraisons échelonnées, avec différentes hauteurs et types de feuillage 🌿.',
            'Qu’est-ce que l’autonomie semencière ?' => 'La capacité à produire, sélectionner et conserver ses propres semences sans dépendre des fournisseurs extérieurs 🌱.',
            'Comment cultiver en sol argileux ?' => 'Améliorez-le avec du compost et du sable, travaillez-le quand il n’est ni trop sec ni trop humide et choisissez des plantes adaptées 🌱.',
            'Quels sont les avantages des engrais verts ?' => 'Ils protègent le sol, l’enrichissent en azote, améliorent sa structure et favorisent la vie microbienne 🌿.',
            
            // 221-1000
            // Ajoutez toutes les questions que vous voulez ici 🔥🔥🔥
        ];

        // Recherche de la réponse appropriée
        foreach ($qaList as $question => $answer) {
            if (stripos($message, $question) !== false) {
                $response = $answer;
                break;
            }
        }

        // Si aucune réponse n'est trouvée
        if (empty($response)) {
            $response = 'Désolé, je n\'ai pas compris. Pouvez-vous reformuler ? 🤔';
        }

        return new JsonResponse(['message' => $response]);
    }

    #[Route('/chat', name: 'chat')]
    public function chat(SessionInterface $session, EntityManagerInterface $entityManager)
    {

        $qaList = [
            // 1-20: Questions Générales
            'Qu’est-ce que l’agriculture biologique ?' => 'L’agriculture biologique utilise des méthodes naturelles sans produits chimiques pour cultiver des plantes et élever des animaux.',
            'Quels sont les types de cultures les plus rentables ?' => 'Les cultures comme la tomate 🍅, le blé 🌾, l’olive 🫒 et l’amande sont souvent rentables selon la région.',
            'Comment améliorer la productivité des cultures ?' => 'Utilisez des semences certifiées, l’irrigation goutte-à-goutte et la rotation des cultures.',
            'Quel est le meilleur engrais naturel ?' => 'Le compost, le fumier de vache et le fumier de volaille sont d’excellents engrais organiques.',
            'Comment gérer les mauvaises herbes ?' => 'Utilisez la technique de paillage, la rotation des cultures et le désherbage manuel.',
            
            // 21-40: Irrigation
            'Quels sont les types d’irrigation utilisés en agriculture ?' => 'Goutte-à-goutte, irrigation par aspersion, et irrigation de surface.',
            'Comment économiser l’eau lors de l’irrigation ?' => 'Utilisez l’irrigation goutte-à-goutte et récoltez l’eau de pluie.',
            'Quels sont les avantages de l’irrigation goutte-à-goutte ?' => 'Économie d’eau, réduction des maladies, et meilleur contrôle de l’humidité.',
            'Quelle est la meilleure heure pour irriguer ?' => 'Tôt le matin ou en fin d’après-midi pour éviter l’évaporation.',
            
            // 41-60: Fertilisation
            'Quel engrais utiliser pour les tomates ?' => 'Utilisez des engrais riches en potassium et en phosphore.',
            'Quand faut-il appliquer le compost ?' => 'Avant la plantation et pendant la croissance des plantes.',
            'Quels sont les signes de carence en azote dans les plantes ?' => 'Feuilles jaunies et croissance lente.',
            'Quels sont les meilleurs fertilisants biologiques ?' => 'Compost, fumier et algues marines.',
            
            // 61-80: Lutte contre les ravageurs
            'Comment lutter naturellement contre les pucerons ?' => 'Utilisez du savon noir ou des coccinelles 🐞.',
            'Quels insectes sont bénéfiques pour les cultures ?' => 'Les coccinelles, les abeilles et les vers de terre.',
            'Qu’est-ce que la lutte biologique ?' => 'Utilisation d’insectes ou de micro-organismes pour contrôler les ravageurs.',
            
            // 81-100: Gestion des sols
            'Comment améliorer la fertilité du sol ?' => 'Ajoutez du compost et pratiquez la rotation des cultures.',
            'Quels sont les tests pour vérifier la qualité du sol ?' => 'Test de pH, test de texture et test de matière organique.',
            
            // 101-120: Agriculture durable
            'Qu’est-ce que l’agriculture de conservation ?' => 'Techniques visant à préserver les sols et à économiser l’eau.',
            'Comment réduire l’utilisation de pesticides ?' => 'Utilisez la lutte biologique et des répulsifs naturels.',
            
            // 121-140: Machines agricoles
            'Quels sont les outils agricoles indispensables ?' => 'Tracteurs, semoirs, pulvérisateurs et moissonneuses.',
            'Comment entretenir une moissonneuse ?' => 'Nettoyez les filtres, vérifiez les niveaux d’huile et graissez les pièces mobiles.',
            
            // 141-160: Ventes & commercialisation
            'Comment vendre ses produits agricoles ?' => 'Marchés locaux, coopératives et ventes en ligne.',
            'Quels sont les avantages de la vente directe ?' => 'Prix plus élevés, relation directe avec le client.',
            
            // 161-180: Agriculture intelligente
            'Qu’est-ce que l’agriculture intelligente ?' => 'Utilisation des capteurs, drones et logiciels pour optimiser la production.',
            'Comment utiliser les drones en agriculture ?' => 'Surveillance des cultures, pulvérisation de pesticides et cartographie des sols.',
            
            // 181-200: Subventions & Financement
            'Comment obtenir des subventions agricoles ?' => 'Présentez une demande auprès du ministère de l’agriculture ou des ONG.',
            'Quels sont les documents nécessaires pour obtenir un crédit agricole ?' => 'Plan d’affaires, carte d’identité, titre foncier et devis des équipements.',
            
            // 201-220: Stockage des récoltes
            'Comment stocker les céréales sans pesticides ?' => 'Utilisez des silos hermétiques et des feuilles de neem.',
            'Quels sont les meilleurs sacs pour le stockage des céréales ?' => 'Sacs hermétiques en polyéthylène.',
            'Bonjour' => 'Bonjour 🌾! Comment puis-je vous aider ?',
            'Comment entretenir mes plantes ?' => 'Arrosez vos plantes régulièrement et utilisez des engrais naturels 🌿.',
            'Quel type de culture recommandez-vous ?' => 'Je recommande la culture de tomates 🍅, blé 🌾 et pommes de terre 🥔 en hiver.',
            'Qu’est-ce que l’agriculture biologique ?' => 'L’agriculture biologique est un système de production qui exclut l’usage de produits chimiques de synthèse, favorisant des pratiques respectueuses de l’environnement.',
            'Quels sont les avantages de la rotation des cultures ?' => 'La rotation des cultures aide à prévenir l’épuisement des sols, réduit les maladies et les parasites, et améliore la fertilité du sol.',
            'Comment puis-je améliorer la qualité de mon sol ?' => 'L’ajout de compost, la rotation des cultures et l’utilisation de cultures de couverture peuvent améliorer la qualité du sol.',
            'Quels sont les principaux défis de l’agriculture moderne ?' => 'Les défis incluent le changement climatique, la gestion durable des ressources, et la sécurité alimentaire.',
            'Qu’est-ce que l’agriculture de conservation ?' => 'C’est une approche qui vise à améliorer la durabilité en minimisant le travail du sol, en maintenant une couverture permanente du sol, et en diversifiant les cultures.',
            'Comment gérer efficacement l’eau dans l’agriculture ?' => 'L’irrigation efficace, la collecte des eaux de pluie, et l’utilisation de cultures résistantes à la sécheresse sont des stratégies clés.',
            'Quels sont les impacts du changement climatique sur l’agriculture ?' => 'Le changement climatique peut entraîner des conditions météorologiques extrêmes, affectant les rendements des cultures et la sécurité alimentaire.',
            'Qu’est-ce que la permaculture ?' => 'Un système agricole qui imite les écosystèmes naturels pour créer des cultures durables et résilientes 🌱.',
            'Comment faire du compost ?' => 'Mélangez des déchets verts (épluchures) et bruns (feuilles sèches), maintenez humide et retournez régulièrement ♻️.',
            'Quels sont les meilleurs légumes à cultiver pour un débutant ?' => 'Les radis, salades, courgettes et haricots verts sont faciles à cultiver pour les débutants 🥗.',
            'Comment lutter contre les pucerons naturellement ?' => 'Utilisez du savon noir dilué, des coccinelles ou une décoction d’ortie pour éliminer les pucerons 🐞.',
            'Qu’est-ce que l’agroforesterie ?' => 'C’est l’association d’arbres et de cultures ou d’élevage sur une même parcelle pour créer des synergies bénéfiques 🌳.',
            'Comment reconnaître un sol fertile ?' => 'Un sol fertile est riche en matière organique, bien drainé, aéré et contient de nombreux organismes vivants comme les vers de terre 🌱.',
            'Quelles sont les meilleures périodes pour planter des pommes de terre ?' => 'Plantez les pommes de terre entre mars et avril pour une récolte estivale 🥔.',
            'Comment utiliser le purin d’ortie ?' => 'Utilisez-le dilué à 10% comme engrais et à 20% comme insecticide naturel 🌿.',
            'Quels sont les avantages de l’agriculture urbaine ?' => 'Elle réduit les îlots de chaleur, améliore la sécurité alimentaire locale et favorise la biodiversité en ville 🏙️.',
            'Comment conserver les semences ?' => 'Stockez-les dans un endroit frais, sec et à l’abri de la lumière dans des contenants hermétiques 🌰.',
            'Qu’est-ce que la lutte biologique ?' => 'C’est l’utilisation d’organismes vivants pour contrôler les ravageurs et les maladies des cultures 🐛.',
            'Comment installer un système d’irrigation goutte-à-goutte ?' => 'Connectez un tuyau principal à une source d’eau, puis ajoutez des goutteurs près de chaque plante pour un arrosage économique 💧.',
            'Quels sont les critères d’un label bio ?' => 'Absence de produits chimiques de synthèse, respect du bien-être animal et de la biodiversité, traçabilité des produits 🌿.',
            'Comment prévenir l’érosion des sols ?' => 'Plantez des haies, utilisez des cultures de couverture et pratiquez le labour minimal pour protéger la structure du sol 🌎.',
            'Qu’est-ce que l’hydroponie ?' => 'C’est une méthode de culture hors-sol où les plantes poussent dans une solution nutritive sans terre 💧.',
            'Comment attirer les pollinisateurs dans mon jardin ?' => 'Plantez des fleurs riches en nectar comme la lavande, le tournesol ou le thym, et évitez les pesticides 🐝.',
            'Quels sont les avantages des semences paysannes ?' => 'Elles préservent la biodiversité, sont adaptées aux terroirs locaux et permettent l’autonomie des agriculteurs 🌱.',
            'Comment faire une butte en permaculture ?' => 'Superposez des couches de matière organique (bois, compost, paille) pour créer un sol fertile et auto-entretenu 🌄.',
            'Qu’est-ce que l’agriculture régénératrice ?' => 'Une approche qui vise à restaurer la santé des sols et des écosystèmes tout en produisant des aliments nutritifs 🌱.',
            'Comment tailler un arbre fruitier ?' => 'Éliminez les branches mortes, aérez le centre et conservez une forme équilibrée pendant la période de dormance ✂️.',
            'Quels sont les principes de l’agriculture biodynamique ?' => 'Elle considère la ferme comme un organisme vivant et utilise des préparations spécifiques selon les rythmes cosmiques 🌙.',
            'Comment choisir les variétés adaptées à mon climat ?' => 'Consultez les catalogues régionaux, observez ce qui pousse localement et privilégiez les variétés résistantes aux conditions de votre région 🌡️.',
            'Qu’est-ce que la méthode Fukuoka ?' => 'Une approche d’agriculture naturelle qui minimise l’intervention humaine et laisse agir les processus naturels 🍃.',
            'Comment planifier une rotation des cultures efficace ?' => 'Alternez les familles botaniques et les besoins nutritifs (légumineuses puis légumes-fruits puis légumes-feuilles) 🔄.',
            'Quels sont les avantages du paillage ?' => 'Il conserve l’humidité, limite les mauvaises herbes et enrichit progressivement le sol en se décomposant 🍂.',
            'Comment faire ses propres semences ?' => 'Sélectionnez les meilleures plantes, laissez-les monter en graines, récoltez à maturité et stockez dans un lieu sec 🌱.',
            'Qu’est-ce que la phytoremédiation ?' => 'L’utilisation de plantes pour dépolluer des sols contaminés par des métaux lourds ou d’autres polluants 🌿.',
            'Comment cultiver des champignons ?' => 'Utilisez un substrat adapté (paille, marc de café), ensemencez avec du mycélium et maintenez l’humidité 🍄.',
            'Quels sont les principes de la traction animale moderne ?' => 'Une alternative écologique aux tracteurs, utilisant des animaux formés avec des outils adaptés pour travailler le sol 🐎.',
            'Comment réaliser un diagnostic de sol ?' => 'Observez sa texture, sa structure, mesurez son pH et identifiez les plantes bio-indicatrices présentes 🔍.',
            'Qu’est-ce que l’agriculture verticale ?' => 'Un système de production en hauteur, souvent en intérieur, qui optimise l’espace en cultivant sur plusieurs niveaux 🏙️.',
            'Comment composter en appartement ?' => 'Utilisez un lombricomposteur qui transforme vos déchets organiques en compost grâce à l’action des vers 🪱.',
            'Quels sont les avantages des légumineuses dans la rotation ?' => 'Elles fixent l’azote atmosphérique dans le sol, enrichissant naturellement la terre pour les cultures suivantes 🌱.',
            'Comment lutter contre le mildiou de la tomate ?' => 'Évitez l’humidité sur le feuillage, espacez les plants et utilisez des préparations à base de prêle ou de bicarbonate 🍅.',
            'Qu’est-ce que l’agroécologie ?' => 'Une approche qui applique les principes écologiques à l’agriculture pour la rendre plus durable et résiliente 🌍.',
            'Comment réaliser une greffe d’arbre fruitier ?' => 'Assemblez un greffon (variété désirée) sur un porte-greffe compatible en assurant un bon contact des cambiums 🌳.',
            'Quels sont les principes de la biodynamie ?' => 'Elle considère l’exploitation comme un organisme vivant et utilise des préparations spécifiques selon le calendrier lunaire 🌕.',
            'Comment créer une haie biodiversifiée ?' => 'Mélangez des espèces locales à floraisons échelonnées, avec différentes hauteurs et types de feuillage 🌿.',
            'Qu’est-ce que l’autonomie semencière ?' => 'La capacité à produire, sélectionner et conserver ses propres semences sans dépendre des fournisseurs extérieurs 🌱.',
            'Comment cultiver en sol argileux ?' => 'Améliorez-le avec du compost et du sable, travaillez-le quand il n’est ni trop sec ni trop humide et choisissez des plantes adaptées 🌱.',
            'Quels sont les avantages des engrais verts ?' => 'Ils protègent le sol, l’enrichissent en azote, améliorent sa structure et favorisent la vie microbienne 🌿.',
            
            // 221-1000
            // Ajoutez toutes les questions que vous voulez ici 🔥🔥🔥
        ];

        $loggedInUserId = $session->get('user_id');

        if (!$loggedInUserId) {
            return $this->redirectToRoute('app_user_loginback');
        }
        $loggedInUser = $entityManager->getRepository(User::class)->find($loggedInUserId);
        if (!$loggedInUser) {
            return $this->redirectToRoute('app_user_loginback');
        }

        return $this->render('chatbot/index.html.twig', [
            'questions' => $qaList,
            'loggedInUser' => $loggedInUser,
        ]);
    }
}
