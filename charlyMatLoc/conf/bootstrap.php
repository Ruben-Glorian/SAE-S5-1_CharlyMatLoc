<?php

use charlyMatLoc\src\api\middlewares\CorsMiddleware;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ );
$dotenv->load();

$builder = new ContainerBuilder();
$builder->useAutowiring(false);
$builder->addDefinitions(__DIR__ . '/settings.php');
$builder->addDefinitions(__DIR__ . '/services.php');
$builder->addDefinitions(__DIR__ . '/api.php');
$c = $builder->build();

$app = AppFactory::createFromContainer($c);

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->addErrorMiddleware($c->get('settings')['displayErrorDetails'], false, false)
    ->getDefaultErrorHandler()
    ->forceContentType('application/json')
;
$app->add(new CorsMiddleware());

$twig = Twig::create(__DIR__ . '/../webui/views', ['cache' => false]);
$app->add(TwigMiddleware::create($app, $twig));
$app = (require_once __DIR__ . '/routes.php')($app);

return $app;