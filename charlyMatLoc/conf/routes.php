<?php
declare(strict_types=1);

use charlyMatLoc\src\api\actions\ajoutPanierAction;
use charlyMatLoc\src\api\actions\getCatalogueAction;
use charlyMatLoc\src\api\actions\GetDetailsOutilsAction;
use charlyMatLoc\src\api\actions\getPanierAction;
use charlyMatLoc\src\api\actions\SignInAction;
use charlyMatLoc\src\api\actions\SignUpAction;
use charlyMatLoc\src\api\middlewares\CorsMiddleware;
use charlyMatLoc\webui\actions\getCatalogueViewAction;
use charlyMatLoc\webui\actions\SigninViewAction;
use charlyMatLoc\webui\actions\ConnectedViewAction;
use charlyMatLoc\webui\actions\ReservationsViewAction;

return function(\Slim\App $app):\Slim\App {
    //Routes des apis
    $app->get('/catalogue/api', getCatalogueAction::class);
    $app->get('/panier/api', getPanierAction::class);
    $app->post('/api/panier/ajouter', ajoutPanierAction::class);
    $app->post('/signup', SignUpAction::class)->setName('signup');
    $app->post('/signin', SignInAction::class);

    //Routes pour les twigs
    $app->get('/catalogue', getCatalogueViewAction::class);
    $app->get('/connected', ConnectedViewAction::class);
    $app->get('/signin', SigninViewAction::class);
    $app->get('/reservations', ReservationsViewAction::class);

    //Routes qui marchent pour les 2
    $app->get('/outils/{id}', getDetailsOutilsAction::class);

    //Middleware CORS
    $app->add(new CorsMiddleware());
    return $app;
};