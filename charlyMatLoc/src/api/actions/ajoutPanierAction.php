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
        $data = $request->getParsedBody();
        $outil_id = $data['outil_id'] ?? null;
        $date_location = $data['date'] ?? null;
        $authHeader = $request->getHeaderLine('Authorization');
        //vérif de la présence du token Bearer
        if (!preg_match('/Bearer (.+)/', $authHeader, $matches)) {
            $response->getBody()->write(json_encode(['error' => 'Authentification requise.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        $token = $matches[1];
        try {
            //décodage du token jwt pour recup l'id utilisateur
            $payload = $this->jwtManager->decodeToken($token);
            $user_id = $payload['sub'] ?? null;
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode(['error' => 'Token invalide: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
        }
        if (!$outil_id || !$date_location || !$user_id) {
            $response->getBody()->write(json_encode(['error' => 'Outil, date ou utilisateur manquant.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
        //doublons dans le panier
        if ($this->panierRepository->verifDoublons($outil_id, $date_location, $user_id)) {
            $response->getBody()->write(json_encode(['error' => 'Cet outil est deja dans le panier pour cette date.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }
        $panier = new Panier(
            0,
            $outil_id,
            $date_location,
            date('Y-m-d H:i:s')
        );
        //ajout de l'outil au panier via le repository
        $this->panierRepository->ajouterOutil($outil_id, $date_location, $user_id);
        $response->getBody()->write(json_encode([
            'message' => 'Outil ajoute au panier',
            'panier' => [
                'outil_id' => $outil_id,
                'date_location' => $date_location,
                'date_ajout' => $panier->getDateAjout()
            ]
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
}