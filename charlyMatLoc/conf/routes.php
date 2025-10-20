<?php
declare(strict_types=1);

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Factory\AppFactory;
use charlyMatLoc\src\api\actions\getCatalogueAction;
use charlyMatLoc\src\api\actions\getDetailsOutilsAction;

return function( \Slim\App $app):\Slim\App {
    $app->get('/catalogue', \charlyMatLoc\src\api\actions\getCatalogueAction::class);
    $app->get('/outils/{id}', \charlyMatLoc\src\api\actions\getDetailsOutilsAction::class);
    $app->get('/catalogue/html', \charlyMatLoc\src\api\actions\getCatalogueHtmlAction::class);
    return $app;
};