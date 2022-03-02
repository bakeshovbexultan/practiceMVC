<?php

use DI\ContainerBuilder;
use League\Plates\Engine;
use Delight\Auth\Auth;
use Aura\SqlQuery\QueryFactory;

$containerBuilder = new ContainerBuilder;
$containerBuilder->addDefinitions([
    Engine::class => function() {
        return new Engine('../app/views');
    },
    PDO::class => function() {
        return new PDO("mysql:host=localhost;dbname=marlincom", "root", "");
    },
    Auth::class => function($container) {
        return new Auth($container->get('PDO'));
    },
    QueryFactory::class => function() {
        return new QueryFactory('mysql');
    },
]);
$container = $containerBuilder->build();

$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
    $r->addRoute('GET', '/', ['App\controllers\RegisterController', 'page_register']);
    $r->addRoute('GET', '/page_register', ['App\controllers\RegisterController', 'page_register']);
    $r->addRoute('POST', '/register', ['App\controllers\RegisterController', 'register']);

    $r->addRoute('GET', '/page_login', ['App\controllers\LoginController', 'page_login']);
    $r->addRoute('POST', '/login', ['App\controllers\LoginController', 'login']);

    $r->addRoute('GET', '/users', ['App\controllers\UsersController', 'listOfUsers']);

    $r->addRoute('GET', '/create_user', ['App\controllers\CreateUserController', 'create_user']);
    $r->addRoute(['POST', 'GET'], '/add_user', ['App\controllers\CreateUserController', 'add_user']);

    $r->addRoute('GET', '/page_edit{id:\d+}', ['App\controllers\EditUserController', 'edit_user']);///edit\?id=10
    $r->addRoute('GET', '/edit', ['App\controllers\EditUserController', 'edit']);

    $r->addRoute('GET', '/page_profile{id:\d+}', ['App\controllers\UserProfileController', 'page_profile']);
    $r->addRoute('GET', '/logout', ['App\controllers\UserProfileController', 'logout']);
    $r->addRoute('GET', '/delete_profile{id:\d+}', ['App\controllers\UserProfileController', 'delete_profile']);

    $r->addRoute('GET', '/page_security{id:\d+}', ['App\controllers\EditSecurityController', 'page_security']);
    $r->addRoute('POST', '/editSecurity', ['App\controllers\EditSecurityController', 'editSecurity']);

    $r->addRoute('GET', '/page_status_edit{id:\d+}', ['App\controllers\EditStatusOfUserController', 'page_status_edit']);///edit\?id=10
    $r->addRoute('POST', '/editUserStatus', ['App\controllers\EditStatusOfUserController', 'editUserStatus']);

    $r->addRoute('GET', '/page_media{id:\d+}', ['App\controllers\EditUserAvatarController', 'page_media']);
    $r->addRoute('POST', '/editAvatar', ['App\controllers\EditUserAvatarController', 'editAvatar']);


});