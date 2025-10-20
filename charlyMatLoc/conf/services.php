<?php

use charlyMatLoc\src\api\actions\getCatalogueAction;
use charlyMatLoc\src\api\actions\getDetailsOutilsAction;
use charlyMatLoc\src\api\actions\getPanierAction;
use charlyMatLoc\src\application_core\application\ports\spi\CatalogueRepositoryInterface;
use charlyMatLoc\src\application_core\application\ports\spi\PanierRepositoryInterface;
use charlyMatLoc\src\infrastructure\repositories\PDOCatalogueRepository;
use charlyMatLoc\src\infrastructure\repositories\PDOPanierRepository;
use charlyMatLoc\webui\actions\getCatalogueViewAction;

return[
    'pdo' => function($container) {
        $settings = $container->get('settings')['db_catalogue'];
        return new \PDO($settings['dsn'], $settings['user'], $settings['password']);
    },
    CatalogueRepositoryInterface::class => function($container) {
        return new PDOCatalogueRepository($container->get('pdo'));
    },
    getCatalogueAction::class => function($container) {
        return new getCatalogueAction($container->get(CatalogueRepositoryInterface::class));
    },
    getDetailsOutilsAction::class => function ($container) {
        return new getDetailsOutilsAction($container->get(CatalogueRepositoryInterface::class));
    },
    getCatalogueViewAction::class => function($container) {
        return new getCatalogueViewAction(
            $container->get(CatalogueRepositoryInterface::class)
        );
    },
    PanierRepositoryInterface::class => function($container) {
        return new PDOPanierRepository($container->get('pdo'));
    },
    getPanierAction::class => function ($container) {
        return new getPanierAction($container->get(PanierRepositoryInterface::class));
    }
];