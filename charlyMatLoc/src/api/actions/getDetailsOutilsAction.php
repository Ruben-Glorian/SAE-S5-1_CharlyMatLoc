<?php

namespace charlyMatLoc\src\api\actions;

use charlyMatLoc\src\infrastructure\repositories\PDOCatalogueRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class getDetailsOutilsAction extends AbstractAction
{
    private PDOCatalogueRepository $catalogueRepository;

    public function __construct(PDOCatalogueRepository $catalogueRepository)
    {
        $this->catalogueRepository = $catalogueRepository;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        //recup l'id de l'outil depuis les paramètres de la requête
        $outilId = (int) ($args['id'] ?? 0);

        //verif que l'id est valide
        if ($outilId <= 0) {
            $rs->getBody()->write(json_encode(['error' => 'ID outil invalide']));
            return $rs->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        //recup l'outil via le repository
        $outil = $this->catalogueRepository->detailsOutil($outilId);
        //404
        if (!$outil) {
            $rs->getBody()->write(json_encode(['error' => 'Outil non trouvé']));
            return $rs->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $rs->getBody()->write(json_encode($outil->toArray()));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}
