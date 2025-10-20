<?php

namespace charlyMatLoc\webui\actions;

use charlyMatLoc\src\api\actions\AbstractAction;
use charlyMatLoc\src\application_core\application\ports\spi\CatalogueRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class getCatalogueViewAction extends AbstractAction {
    private $serviceCatalogue;

    public function __construct(CatalogueRepositoryInterface $serviceCatalogue) {
        $this->serviceCatalogue = $serviceCatalogue;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
        $catalogue = $this->serviceCatalogue->listerOutils();
        $view = Twig::fromRequest($request);
        return $view->render($response, 'catalogue.twig', [
            'catalogue' => $catalogue
        ]);
    }
}
