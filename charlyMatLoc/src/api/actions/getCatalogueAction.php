<?php

namespace charlyMatLoc\src\api\actions;

use charlyMatLoc\src\application_core\application\ports\spi\CatalogueRepositoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class getCatalogueAction extends AbstractAction{
    private $serviceCatalogue;

    public function __construct(CatalogueRepositoryInterface $serviceCatalogue){
        $this->serviceCatalogue = $serviceCatalogue;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface{
        $catalogue = $this->serviceCatalogue->listerOutils();
        $catalogueArray = array_map(function($o) {
            return is_object($o) && method_exists($o, 'toArray') ? $o->toArray() : $o;
        }, $catalogue);
        $response->getBody()->write(json_encode($catalogueArray));
        //200 ok
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}