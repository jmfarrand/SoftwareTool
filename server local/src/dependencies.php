<?php
// DIC configuration

$container = $app->getContainer();

//Register Twig component in the Slim container
$container['view'] = function ($container) {
    $view = new \Slim\Views\Twig('../templates', [
        'cache' => false
    ]);

    $router = $container->get('router');
    $uri = \Slim\Http\Uri::createFromEnvironment(new \Slim\Http\Environment($_SERVER));
    $view->addExtension(new Slim\Views\TwigExtension($router, $uri));

    return $view;
};

//Set up the database in the Slim container
$container['db'] = function ($container) {
    $dbsettings = $container->get('settings')['db'];

    $dsn = 'mysql:host=' . $dbsettings['host'] .';dbname=' . $dbsettings['database'] . ';charset=utf8mb4';
    $db = new PDO($dsn, $dbsettings['username'], $dbsettings['password']);

    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    
    return $db;
};

//slim-csrf
$container['csrf'] = function($c) {
    return new \Slim\Csrf\Guard;
};
