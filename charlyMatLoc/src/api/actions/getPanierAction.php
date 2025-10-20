<?php

namespace charlyMatLoc\src\api\actions;

use charlyMatLoc\src\infrastructure\repositories\PDOPanierRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class getPanierAction {
    private PDOPanierRepository $servicePanier;

    public function __construct(PDOPanierRepository $servicePanier){
        $this->servicePanier = $servicePanier;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface{
        // On utilise la BDD, pas la session
        $resultat = $this->servicePanier->listerPanier();
        $response->getBody()->write(json_encode([
            'panier' => $resultat['items'],
            'total' => $resultat['total']
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}