<?php
declare(strict_types=1);

use charlyMatLoc\src\api\actions\ajoutPanierAction;
use charlyMatLoc\src\api\actions\getCatalogueAction;
use charlyMatLoc\src\api\actions\GetDetailsOutilsAction;
use charlyMatLoc\src\api\actions\getPanierAction;
use charlyMatLoc\src\api\actions\SignInAction;
use charlyMatLoc\src\api\middlewares\CorsMiddleware;
use charlyMatLoc\webui\actions\getCatalogueViewAction;

return function(\Slim\App $app):\Slim\App {

    //Middleware CORS
    $app->add(new CorsMiddleware());

    //Routes pour les twigs
    $app->get('/catalogue', getCatalogueViewAction::class);

    //Routes pour les api
    $app->get('/catalogue/api', getCatalogueAction::class);

    //Routes pour le detail d'un outil
    $app->get('/outils/{id}', getDetailsOutilsAction::class);

    //Routes pour le panier
    $app->get('/panier/api', getPanierAction::class);

    //Routes pour ajouter un outil au panier
    $app->post('/api/panier/ajouter', ajoutPanierAction::class);

    //Routes pour la connexion
    $app->post('/signin', SignInAction::class)->setName('signin');

    return $app;
};