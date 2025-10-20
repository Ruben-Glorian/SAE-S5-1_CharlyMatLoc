<?php

namespace charlyMatLoc\src\api\actions;

use charlyMatLoc\src\infrastructure\repositories\PDOCatalogueRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class GetDetailsOutilsAction extends AbstractAction
{
    private PDOCatalogueRepository $catalogueRepository;

    public function __construct(PDOCatalogueRepository $catalogueRepository)
    {
        $this->catalogueRepository = $catalogueRepository;
    }

    public function __invoke(ServerRequestInterface $rq, ResponseInterface $rs, array $args): ResponseInterface
    {
        $outilId = (int) ($args['id'] ?? 0);

        if ($outilId <= 0) {
            $rs->getBody()->write(json_encode(['error' => 'ID outil invalide']));
            return $rs->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $outil = $this->catalogueRepository->detailsOutil($outilId);

        if (!$outil) {
            $rs->getBody()->write(json_encode(['error' => 'Outil non trouvé']));
            return $rs->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $rs->getBody()->write(json_encode($outil->toArray()));
        return $rs->withHeader('Content-Type', 'application/json');
    }
}
