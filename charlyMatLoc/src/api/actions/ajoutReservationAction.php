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
        //verif la présence du token jwt dans l'entete authz
        $authHeader = $request->getHeaderLine('Authorization');
        if (!preg_match('/Bearer (.+)/', $authHeader, $matches)) {
            $response->getBody()->write(json_encode(['error' => 'Authentification requise']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        $token = $matches[1];
        try {
            //décode le token et recup l'id utilisateur
            $payload = $this->jwtManager->decodeToken($token);
            $user_id = $payload['sub'] ?? null;
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token invalide']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }

        //verif que l'utilisateur existe
        if (!$user_id) {
            $response->getBody()->write(json_encode(['error' => 'Utilisateur introuvable']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        //recup le panier de l'utilisateur
        $panier = $this->panierRepository->listerPanier($user_id);
        $items = $panier['items'] ?? [];

        //panier vide
        if (empty($items)) {
            $response->getBody()->write(json_encode(['message' => 'Panier vide, rien a valider']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }

        try {
            //transaction sql
            $this->pdo->beginTransaction();

            //chaque outil du panier = crée une réservation
            $outils_reserves = [];
            $countInserted = 0;
            foreach ($items as $item) {
                $outil_id = isset($item['outil_id']) ? (int)$item['outil_id'] : (int)($item['id'] ?? 0);
                $date_location = $item['date_location'] ?? null;

                //ajoute la réservation via le repository
                $this->reservationRepository->ajouterOutil($outil_id, $date_location, $user_id);
                $outils_reserves[$outil_id] = true;
                $countInserted++;
            }

            //on decremente le stock une fois pour chaque outil réservé
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

            //supprime le panier de l'utilisateur
            $stmtDelete = $this->pdo->prepare('DELETE FROM panier WHERE user_id = :user_id');
            $stmtDelete->bindValue(':user_id', $user_id, \PDO::PARAM_STR);
            $stmtDelete->execute();

            //valide la transaction
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