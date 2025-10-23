<?php
declare(strict_types=1);

use charlyMatLoc\src\api\actions\ajoutPanierAction;
use charlyMatLoc\src\api\actions\getCatalogueAction;
use charlyMatLoc\src\api\actions\GetDetailsOutilsAction;
use charlyMatLoc\src\api\actions\getPanierAction;
use charlyMatLoc\src\api\actions\SignInAction;
use charlyMatLoc\src\api\actions\SignUpAction;
use charlyMatLoc\src\api\middlewares\CorsMiddleware;

return function(\Slim\App $app):\Slim\App {
    //Routes des apis
    $app->get('/api/outils', getCatalogueAction::class);
    $app->get('/api/panier', getPanierAction::class);
    $app->post('/api/panier', ajoutPanierAction::class);
    $app->post('/signup', SignUpAction::class)->setName('signup');
    $app->post('/signin', SignInAction::class);
    $app->get('/api/outils/{id}', getDetailsOutilsAction::class);

    //Middleware CORS
    $app->add(new CorsMiddleware());
    return $app;
};