<?php

use charlyMatLoc\src\api\actions\getCatalogueAction;
use charlyMatLoc\src\api\actions\getCatalogueHtmlAction;
use charlyMatLoc\src\api\actions\getDetailsOutilsAction;
use charlyMatLoc\src\application_core\application\ports\spi\CatalogueRepositoryInterface;
use charlyMatLoc\src\infrastructure\repositories\PDOCatalogueRepository;

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
    getCatalogueHtmlAction::class => function($container) {
        return new getCatalogueHtmlAction(
            $container->get(CatalogueRepositoryInterface::class)
        );
    }
];