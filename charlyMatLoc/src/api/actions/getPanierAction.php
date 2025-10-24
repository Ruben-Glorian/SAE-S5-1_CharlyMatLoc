<?php

namespace charlyMatLoc\src\api\actions;

use charlyMatLoc\src\api\providers\JWTManager;
use charlyMatLoc\src\infrastructure\repositories\PDOPanierRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class getPanierAction {
    private PDOPanierRepository $servicePanier;
    private JWTManager $jwtManager;

    public function __construct(PDOPanierRepository $servicePanier, JWTManager $jwtManager){
        $this->servicePanier = $servicePanier;
        $this->jwtManager = $jwtManager;
    }    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface{
        //verif si un id utilisateur est fourni dans l'url
        if (isset($args['id_user'])) {
            $user_id = $args['id_user'];
        } else {
            //recup l'id depuis le token jwt
            $authHeader = $request->getHeaderLine('Authorization');
            //401
            if (!preg_match('/Bearer (.+)/', $authHeader, $matches)) {
                $response->getBody()->write(json_encode(['error' => 'Authentification requise.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
            $token = $matches[1];
            try {
                //decode le token pour obtenir le user_id
                $payload = $this->jwtManager->decodeToken($token);
                $user_id = $payload['sub'] ?? null;
            } catch (\Exception $e) {
                //401
                $response->getBody()->write(json_encode(['error' => 'Token invalide.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
        }
        $resultat = $this->servicePanier->listerPanier($user_id);
        $response->getBody()->write(json_encode([
            'panier' => $resultat['items'],
            'total' => $resultat['total']
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}