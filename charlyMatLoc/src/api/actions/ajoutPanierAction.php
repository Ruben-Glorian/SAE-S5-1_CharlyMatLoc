<?php

namespace charlyMatLoc\src\api\actions;

use charlyMatLoc\src\application_core\domain\entities\Panier;
use charlyMatLoc\src\application_core\application\ports\api\dtos\PanierDTO;
use charlyMatLoc\src\application_core\application\ports\spi\PanierRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ajoutPanierAction extends AbstractAction {
    private PanierRepositoryInterface $panierRepository;

    public function __construct(PanierRepositoryInterface $panierRepository) {
        $this->panierRepository = $panierRepository;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $data = $request->getParsedBody();
        $outil_id = $data['outil_id'] ?? null;
        $date_location = $data['date'] ?? null;

        if (!$outil_id || !$date_location) {
            $response->getBody()->write(json_encode(['error' => 'Outil ou date manquant.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        if ($this->panierRepository->verifDoublons($outil_id, $date_location)) {
            $response->getBody()->write(json_encode(['error' => 'Cet outil est deja dans le panier pour cette date.']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(409);
        }

        $panier = new Panier(
            0,
            $outil_id,
            $date_location,
            date('Y-m-d H:i:s')
        );

        $this->panierRepository->ajouterOutil($outil_id, $date_location);

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
