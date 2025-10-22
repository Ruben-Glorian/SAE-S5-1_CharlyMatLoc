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

return function(\Slim\App $app):\Slim\App {

    //Middleware CORS
    $app->add(new CorsMiddleware());

    //Routes pour les twigs
    $app->get('/catalogue', getCatalogueViewAction::class);
    $app->get('/connected', ConnectedViewAction::class);

    //Routes pour les api
    $app->get('/catalogue/api', getCatalogueAction::class);

    //Routes pour le detail d'un outil
    $app->get('/outils/{id}', getDetailsOutilsAction::class);

    //Routes pour le panier
    $app->get('/panier/api', getPanierAction::class);

    //Routes pour ajouter un outil au panier
    $app->post('/api/panier/ajouter', ajoutPanierAction::class);

    //Route pour s'inscrire
    $app->post('/signup', SignUpAction::class)->setName('signup');

    //Routes pour la connexion
    $app->get('/signin', SigninViewAction::class);
    $app->post('/signin', SignInAction::class);

    return $app;
};