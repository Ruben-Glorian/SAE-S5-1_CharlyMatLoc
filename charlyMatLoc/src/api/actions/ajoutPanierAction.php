<?php

namespace charlyMatLoc\src\api\actions;

use charlyMatLoc\src\application_core\domain\entities\Panier;
use charlyMatLoc\src\application_core\application\ports\api\dtos\PanierDTO;
use charlyMatLoc\src\application_core\application\ports\spi\PanierRepositoryInterface;
use charlyMatLoc\src\api\providers\JWTManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ajoutPanierAction extends AbstractAction {
    private PanierRepositoryInterface $panierRepository;
    private JWTManager $jwtManager;

    public function __construct(PanierRepositoryInterface $panierRepository, JWTManager $jwtManager) {
        $this->panierRepository = $panierRepository;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        //recup des données envoyées en post
        $data = $request->getParsedBody();
        $outil_id = $data['outil_id'] ?? null;
        $date_debut = $data['date_debut'] ?? null;
        $date_fin = $data['date_fin'] ?? null;
        $authHeader = $request->getHeaderLine('Authorization');
        //verif la présence du token Bearer dans l'en tête authz
        if (!preg_match('/Bearer (.+)/', $authHeader, $matches)) {
            $response->getBody()->write(json_encode(['error' => 'Authentification requise.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        $token = $matches[1];
        try {
            //decodage du token jwt pour recup l'id utilisateur
            $payload = $this->jwtManager->decodeToken($token);
            $user_id = $payload['sub'] ?? null;
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token invalide: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        //tous les champs nécessaires doivent être présents
        if (!$outil_id || !$date_debut || !$date_fin || !$user_id) {
            $response->getBody()->write(json_encode(['error' => 'Outil, période ou utilisateur manquant.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        //verif que la date de fin est après la date de début
        if ($date_fin < $date_debut) {
            $response->getBody()->write(json_encode(['error' => 'La date de fin doit être après la date de début.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        //verif de conflit de période dans le panier utilisateur
        if ($this->panierRepository->verifConflitPeriode($outil_id, $user_id, $date_debut, $date_fin)) {
            $response->getBody()->write(json_encode(['error' => 'Vous avez déjà cet outil dans votre panier pour cette période.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }
        //verif de la dispo sur la période donnée
        $stmt = $this->panierRepository->getPDO()->prepare('
            SELECT COUNT(*) FROM (
                SELECT date_location FROM reservations WHERE outil_id = :id AND date_location BETWEEN :debut AND :fin
                UNION ALL
                SELECT date_location FROM panier WHERE outil_id = :id AND user_id != :user_id AND date_location BETWEEN :debut AND :fin
            ) AS indispos
        ');
        $stmt->bindValue(':id', $outil_id, \PDO::PARAM_INT);
        $stmt->bindValue(':debut', $date_debut, \PDO::PARAM_STR);
        $stmt->bindValue(':fin', $date_fin, \PDO::PARAM_STR);
        $stmt->bindValue(':user_id', $user_id, \PDO::PARAM_STR);
        $stmt->execute();
        $indispos = (int)($stmt->fetchColumn());
        //cas de l'outil pas dispo
        if ($indispos > 0) {
            $response->getBody()->write(json_encode(['error' => 'Outil indisponible sur la période demandée.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }
        //on ajoute 1 exemplaire de l'outil pour chaque jour de la période
        $date = $date_debut;
        $ajouts = 0;
        while (strtotime($date) <= strtotime($date_fin)) {
            try {
                //ajout d'une ligne dans le panier pour chaque date
                $this->panierRepository->ajouterOutil($outil_id, $date, $user_id);
                $ajouts++;
            } catch (\PDOException $e) {
                $response->getBody()->write(json_encode(['error' => 'Erreur lors de l\'ajout au panier: ' . $e->getMessage()]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
            }
            //jour suivant
            $date = date('Y-m-d', strtotime($date . ' +1 day'));
        }
        //aucun jour ajouté = erreur
        if ($ajouts === 0) {
            $response->getBody()->write(json_encode(['error' => 'Aucun jour ajouté au panier.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
        //succès réservation
        $response->getBody()->write(json_encode([
            'message' => 'Outil ajouté au panier pour la période',
            'outil_id' => $outil_id,
            'date_debut' => $date_debut,
            'date_fin' => $date_fin,
            'jours_ajoutes' => $ajouts
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}