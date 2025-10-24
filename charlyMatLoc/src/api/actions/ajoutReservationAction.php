<?php
namespace charlyMatLoc\src\api\actions;

use charlyMatLoc\src\application_core\application\ports\spi\PanierRepositoryInterface;
use charlyMatLoc\src\application_core\application\ports\spi\ReservationRepositoryInterface;
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

        //On recupere les lignes du panier de l'utilisateur depuis la bd
        $stmt = $this->pdo->prepare('SELECT id, outil_id, date_location FROM panier WHERE user_id = :user_id ORDER BY id');
        $stmt->bindValue(':user_id', $user_id, \PDO::PARAM_STR);
        $stmt->execute();
        $lignes = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($lignes)) {
            $response->getBody()->write(json_encode(['message' => 'Panier vide, rien a valider']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        try {
            $this->pdo->beginTransaction();

            $ins = $this->pdo->prepare('INSERT INTO reservations (user_id, outil_id, date_location) VALUES (:user_id, :outil_id, :date_location)');
            $del = $this->pdo->prepare('DELETE FROM panier WHERE id = :id');

            $countInserted = 0;
            foreach ($lignes as $ligne) {
                $panierId = (int)($ligne['id'] ?? 0);
                $outilId = isset($ligne['outil_id']) ? (int)$ligne['outil_id'] : 0;
                $dateLocation = $ligne['date_location'] ?? null;

                $ins->bindValue(':user_id', $user_id, \PDO::PARAM_STR);
                $ins->bindValue(':outil_id', $outilId, \PDO::PARAM_INT);
                $ins->bindValue(':date_location', $dateLocation);
                $ins->execute();

                $del->bindValue(':id', $panierId, \PDO::PARAM_INT);
                $del->execute();

                $countInserted++;
            }

            $this->pdo->commit();

            $response->getBody()->write(json_encode([
                'message' => 'Panier valide. Reservations ajoutees.',
                'count' => $countInserted
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
