<?php
declare(strict_types=1);

return function( \Slim\App $app):\Slim\App {
    $app->get('/catalogue/api', \charlyMatLoc\src\api\actions\getCatalogueAction::class);
    $app->get('/outils/{id}', \charlyMatLoc\src\api\actions\getDetailsOutilsAction::class);
    $app->get('/catalogue', \charlyMatLoc\webui\actions\getCatalogueViewAction::class);
    $app->get('/panier', \charlyMatLoc\src\api\actions\getPanierAction::class);
    $app->post('/api/panier/ajouter', \charlyMatLoc\src\api\actions\ajoutPanierAction::class);
    return $app;
};