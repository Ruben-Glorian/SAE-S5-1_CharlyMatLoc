<?php
namespace charlyMatLoc\src\api\actions;

// Importation des interfaces et classes nécessaires
use charlyMatLoc\src\application_core\application\ports\spi\PanierRepositoryInterface;
use charlyMatLoc\src\application_core\application\ports\spi\ReservationRepositoryInterface;
use charlyMatLoc\src\api\providers\JWTManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

// Action pour valider le panier et créer les réservations
class ajoutReservationAction extends AbstractAction {

    // Dépendances injectées
    private PanierRepositoryInterface $panierRepository;
    private \PDO $pdo;
    private JWTManager $jwtManager;
    private ReservationRepositoryInterface $reservationRepository;

    // Constructeur avec injection des dépendances
    public function __construct(
        PanierRepositoryInterface $panierRepository,
        \PDO $pdo,
        JWTManager $jwtManager,
        ReservationRepositoryInterface $reservationRepository
    ) {
        $this->panierRepository = $panierRepository;
        $this->pdo = $pdo;
        $this->jwtManager = $jwtManager;
        $this->reservationRepository = $reservationRepository;
    }

    // Méthode principale appelée lors de la requête HTTP
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        // Vérifie la présence du token JWT dans l'en-tête Authorization
        $authHeader = $request->getHeaderLine('Authorization');
        if (!preg_match('/Bearer (.+)/', $authHeader, $matches)) {
            $response->getBody()->write(json_encode(['error' => 'Authentification requise']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        $token = $matches[1];
        try {
            // Décode le token et récupère l'ID utilisateur
            $payload = $this->jwtManager->decodeToken($token);
            $user_id = $payload['sub'] ?? null;
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token invalide']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        // Vérifie que l'utilisateur existe
        if (!$user_id) {
            $response->getBody()->write(json_encode(['error' => 'Utilisateur introuvable']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        // Récupère le panier de l'utilisateur
        $panier = $this->panierRepository->listerPanier($user_id);
        $items = $panier['items'] ?? [];

        // Si le panier est vide, retourne un message
        if (empty($items)) {
            $response->getBody()->write(json_encode(['message' => 'Panier vide, rien a valider']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        try {
            // Démarre une transaction SQL
            $this->pdo->beginTransaction();

            // Pour chaque outil du panier, crée une réservation
            $outils_reserves = [];
            $countInserted = 0;
            foreach ($items as $item) {
                $outil_id = isset($item['outil_id']) ? (int)$item['outil_id'] : (int)($item['id'] ?? 0);
                $date_location = $item['date_location'] ?? null;

                // Ajoute la réservation via le repository
                $this->reservationRepository->ajouterOutil($outil_id, $date_location, $user_id);
                $outils_reserves[$outil_id] = true;
                $countInserted++;
            }

            // Décrémente le stock une seule fois par outil
            foreach (array_keys($outils_reserves) as $outil_id) {
                $stmtStock = $this->pdo->prepare('SELECT nb_exemplaires FROM outils WHERE id = :id FOR UPDATE');
                $stmtStock->bindValue(':id', $outil_id, \PDO::PARAM_INT);
                $stmtStock->execute();
                $stock = (int)$stmtStock->fetchColumn();

                if ($stock <= 0) {
                    throw new \Exception('Stock insuffisant pour l\'outil ID ' . $outil_id);
                }

                $stmtUpdate = $this->pdo->prepare('UPDATE outils SET nb_exemplaires = nb_exemplaires - 1 WHERE id = :id');
                $stmtUpdate->bindValue(':id', $outil_id, \PDO::PARAM_INT);
                $stmtUpdate->execute();
            }

            // Supprime le panier de l'utilisateur
            $stmtDelete = $this->pdo->prepare('DELETE FROM panier WHERE user_id = :user_id');
            $stmtDelete->bindValue(':user_id', $user_id, \PDO::PARAM_STR);
            $stmtDelete->execute();

            // Valide la transaction
            $this->pdo->commit();

            // Retourne un message de succès
            $response->getBody()->write(json_encode([
                'message' => 'Panier valide. Reservations ajoutees.',
                'count' => $countInserted
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            // En cas d'erreur, annule la transaction et retourne un message d'erreur
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $response->getBody()->write(json_encode(['error' => 'Erreur lors de la validation du panier: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}