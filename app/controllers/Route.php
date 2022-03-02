<?php

if( !session_id() ) @session_start();

include 'DIController.php';

$httpMethod = $_SERVER['REQUEST_METHOD'];
$uri = $_SERVER['REQUEST_URI'];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
switch ($routeInfo[0]) {
    case FastRoute\Dispatcher::NOT_FOUND:
        echo '404';
        break;
    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
        $allowedMethods = $routeInfo[1];
        echo 'Метод не доступен';
        break;
    case FastRoute\Dispatcher::FOUND:
        /*$handler = $routeInfo[1];
        $vars = $routeInfo[2];
        $container->call($routeInfo[1], $routeInfo[2]);*/
        $handler = $routeInfo[1];
        $controller = $handler[0];
        $action = $handler[1];
        $vars = $routeInfo[2];

        $container->call([$controller, $action], [$vars]);
        break;
}