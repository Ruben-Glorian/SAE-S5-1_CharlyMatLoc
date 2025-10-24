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

        //On recupere le panier via le repository
        $panier = $this->panierRepository->listerPanier($user_id);
        $items = $panier['items'] ?? [];

        if (empty($items)) {
            $response->getBody()->write(json_encode(['message' => 'Panier vide, rien a valider']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        try {
            $this->pdo->beginTransaction();

            //On enregistre chaque outil du panier en reservation
            $outils_reserves = [];
            $countInserted = 0;
            foreach ($items as $item) {
                $outil_id = isset($item['outil_id']) ? (int)$item['outil_id'] : (int)($item['id'] ?? 0);
                $date_location = $item['date_location'] ?? null;

                // déléguer l'ajout au repository de réservations
                $this->reservationRepository->ajouterOutil($outil_id, $date_location, $user_id);
                $outils_reserves[$outil_id] = true;
                $countInserted++;
            }

            //On decremente le stock UNE SEULE FOIS par outil(meme si reservé sur plusieurs jours)
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

            //On supprime les entrees du panier
            $stmtDelete = $this->pdo->prepare('DELETE FROM panier WHERE user_id = :user_id');
            $stmtDelete->bindValue(':user_id', $user_id, \PDO::PARAM_STR);
            $stmtDelete->execute();

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
