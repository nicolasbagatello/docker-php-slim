<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

include '../app/vendor/autoload.php';

$app = AppFactory::create();

// very bad bad way to know if our DB connection is working
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

// re versioned example of hello world?
$app->get('/hello/{name}', function ($request, $response, $args) {
    $name = empty($args['name']) ? ' world ' : $args['name'];

    $response->getBody()->write("Hello, " . $name);

    return $response;
});

// make sure you ran all migrations and seeds first using MAKE if not this wont work
$app->get('/db/list', function ($request, $response, $args) {
    try {
        $dsn = 'mysql:host=mysql;dbname=local_env_db;charset=utf8;port=3306';
        $pdo = new PDO($dsn, 'dev', 'devPass');

        $response->getBody()->write(
            json_encode(
                array_pop(
                    $pdo->query('select * from test limit 1')->fetchAll(PDO::FETCH_ASSOC)
                )
            )
        );

        return $response;
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
});

$app->run();
