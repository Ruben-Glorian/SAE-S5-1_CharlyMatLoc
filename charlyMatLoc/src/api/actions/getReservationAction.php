<?php

namespace charlyMatLoc\src\api\actions;

use charlyMatLoc\src\api\providers\JWTManager;
use charlyMatLoc\src\application_core\application\ports\spi\ReservationRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class getReservationAction {
    private ReservationRepositoryInterface $serviceReservation;
    private JWTManager $jwtManager;

    public function __construct(ReservationRepositoryInterface $serviceReservation, JWTManager $jwtManager){
        $this->serviceReservation = $serviceReservation;
        $this->jwtManager = $jwtManager;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface{
        if (isset($args['id_user'])) {
            $user_id = $args['id_user'];
        } else {
            $authHeader = $request->getHeaderLine('Authorization');
            if (!preg_match('/Bearer (.+)/', $authHeader, $matches)) {
                $response->getBody()->write(json_encode(['error' => 'Authentification requise.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
            $token = $matches[1];
            try {
                $payload = $this->jwtManager->decodeToken($token);
                $user_id = $payload['sub'] ?? null;
                if (empty($user_id)) {
                    $response->getBody()->write(json_encode(['error' => 'User id absent dans le token.']));
                    return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
                }
            } catch (\Exception $e) {
                $response->getBody()->write(json_encode(['error' => 'Token invalide.']));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }
        }

        try {
            $result = $this->serviceReservation->listerReservations($user_id);
            $items = $result['items'] ?? $result;

            $response->getBody()->write(json_encode([
                'reservations' => $items,
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        } catch (\Throwable $e) {
            $response->getBody()->write(json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
