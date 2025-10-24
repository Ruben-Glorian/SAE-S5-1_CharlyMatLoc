<?php
// PHP
namespace charlyMatLoc\src\api\actions;

use charlyMatLoc\src\application_core\application\ports\spi\PanierRepositoryInterface;
use charlyMatLoc\src\application_core\application\ports\spi\ReservationRepositoryInterface;
use charlyMatLoc\src\infrastructure\repositories\PDOReservationRepository;
use charlyMatLoc\src\api\providers\JWTManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ajoutReservationAction extends AbstractAction {
    private PanierRepositoryInterface $panierRepository;
    private \PDO $pdo;
    private JWTManager $jwtManager;
    private ReservationRepositoryInterface $reservationRepository;

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

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $authHeader = $request->getHeaderLine('Authorization');
        if (!preg_match('/Bearer (.+)/', $authHeader, $matches)) {
            $response->getBody()->write(json_encode(['error' => 'Authentification requise']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        $token = $matches[1];
        try {
            $payload = $this->jwtManager->decodeToken($token);
            $user_id = $payload['sub'] ?? null;
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token invalide']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        if (!$user_id) {
            $response->getBody()->write(json_encode(['error' => 'Utilisateur introuvable']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        $panier = $this->panierRepository->listerPanier($user_id);
        $items = $panier['items'] ?? [];

        if (empty($items)) {
            $response->getBody()->write(json_encode(['message' => 'Panier vide, rien a valider']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        try {
            $this->pdo->beginTransaction();

            foreach ($items as $item) {
                $outil_id = isset($item['outil_id']) ? (int)$item['outil_id'] : (int)($item['id'] ?? 0);
                $date_location = $item['date_location'] ?? null;
                //verif du stock
                $stmtStock = $this->pdo->prepare('SELECT nb_exemplaires FROM outils WHERE id = :id');
                $stmtStock->bindValue(':id', $outil_id, \PDO::PARAM_INT);
                $stmtStock->execute();
                $stock = (int)$stmtStock->fetchColumn();
                if ($stock <= 0) {
                    throw new \Exception('Stock insuffisant pour l\'outil ID ' . $outil_id);
                }
                //ajout reservation
                $this->reservationRepository->ajouterOutil($outil_id, $date_location, $user_id);
                //on réduit le stock
                $stmtUpdate = $this->pdo->prepare('UPDATE outils SET nb_exemplaires = nb_exemplaires - 1 WHERE id = :id');
                $stmtUpdate->bindValue(':id', $outil_id, \PDO::PARAM_INT);
                $stmtUpdate->execute();
            }

            //suppression des entrées du panier pour l'utilisateur
            $stmtDelete = $this->pdo->prepare('DELETE FROM panier WHERE user_id = :user_id');
            $stmtDelete->bindValue(':user_id', $user_id, \PDO::PARAM_STR);
            $stmtDelete->execute();

            $this->pdo->commit();

            $response->getBody()->write(json_encode([
                'message' => 'Panier valide. Reservations ajoutees.',
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
        } catch (\Exception $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            $response->getBody()->write(json_encode(['error' => 'Erreur lors de la validation du panier: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
