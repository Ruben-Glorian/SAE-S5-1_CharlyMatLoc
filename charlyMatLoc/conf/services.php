<?php

use charlyMatLoc\src\api\actions\ajoutPanierAction;
use charlyMatLoc\src\api\actions\getCatalogueAction;
use charlyMatLoc\src\api\actions\GetDetailsOutilsAction;
use charlyMatLoc\src\api\actions\getPanierAction;
use charlyMatLoc\src\api\actions\ajoutReservationAction;
use charlyMatLoc\src\api\actions\getReservationAction;
use charlyMatLoc\src\api\actions\SignInAction;
use charlyMatLoc\src\api\actions\SignUpAction;
use charlyMatLoc\src\api\middlewares\CorsMiddleware;
use charlyMatLoc\src\api\providers\AuthnProviderInterface;
use charlyMatLoc\src\api\providers\JWTAuthnProvider;
use charlyMatLoc\src\application_core\application\ports\spi\AuthRepositoryInterface;
use charlyMatLoc\src\application_core\application\ports\spi\CatalogueRepositoryInterface;
use charlyMatLoc\src\application_core\application\ports\spi\PanierRepositoryInterface;
use charlyMatLoc\src\application_core\application\ports\spi\ReservationRepositoryInterface;
use charlyMatLoc\src\infrastructure\repositories\PDOAuthRepository;
use charlyMatLoc\src\infrastructure\repositories\PDOCatalogueRepository;
use charlyMatLoc\src\infrastructure\repositories\PDOPanierRepository;
use charlyMatLoc\src\infrastructure\repositories\PDOReservationRepository;

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

    GetDetailsOutilsAction::class => function ($container) {
        return new GetDetailsOutilsAction(
            $container->get(CatalogueRepositoryInterface::class)
        );
    },
    PanierRepositoryInterface::class => function($container) {
        return new PDOPanierRepository($container->get('pdo'));
    },
    getPanierAction::class => function ($container) {
        return new getPanierAction(
            $container->get(PanierRepositoryInterface::class),
            $container->get('JWTManager')
        );
    },
    getReservationAction::class => function ($container) {
        return new getReservationAction(
            $container->get(ReservationRepositoryInterface::class),
            $container->get('JWTManager')
        );
    },
    ajoutPanierAction::class => function($container) {
        return new \charlyMatLoc\src\api\actions\ajoutPanierAction(
            $container->get(PanierRepositoryInterface::class),
            $container->get('JWTManager')
        );
    },
    ajoutReservationAction::class => function($container) {
        return new ajoutReservationAction(
            $container->get(PanierRepositoryInterface::class),
            $container->get('pdo'),
            $container->get('JWTManager'),
            $container->get(ReservationRepositoryInterface::class)
        );
    },
    ReservationRepositoryInterface::class => function($container) {
        return new PDOReservationRepository($container->get('pdo'));
    },
    AuthnProviderInterface::class => function ($container) {
        return new JWTAuthnProvider(
            $container->get('JWTManager'),
            $container->get('ServiceUser')
        );
    },
    SignInAction::class => function ($container) {
        return new SignInAction(
            $container->get(AuthnProviderInterface::class)
        );
    },
    AuthRepositoryInterface::class => function ($container) {
        return new PDOAuthRepository($container->get('pdo'));
    },
    JWTAuthnProvider::class => function($container) {
        return new JWTAuthnProvider(
            $container->get('JWTManager'),
            $container->get('ServiceUser')
        );
    },
    SignUpAction::class => function ($container) {
        return new SignUpAction(
            $container->get(JWTAuthnProvider::class)
        );
    },
    'JWTManager' => function($container) {
        $jwtKey = $container->get('settings')['jwt']['key'];
        return new \charlyMatLoc\src\api\providers\JWTManager($jwtKey);
    },
    'ServiceUser' => function($container) {
        return new \charlyMatLoc\src\application_core\application\usecases\ServiceUser(
            $container->get(AuthRepositoryInterface::class)
        );
    },
];