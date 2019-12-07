<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

include '../app/vendor/autoload.php';

$app = AppFactory::create();

$app->get('/db', function (Request $request, Response $response, $args) {
    try {
        $dsn = 'mysql:host=mysql;dbname=local_env_db;charset=utf8;port=3306';
        $pdo = new PDO($dsn, 'dev', 'devPass');
        $response->getBody()->write("DB connected successfully!");
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return $response;
});

$app->get('/hello/{name}', function ($request, $response, $args) {
    $response->getBody()->write("Hello, " . $args['name']);

    return $response;
});

$app->run();
