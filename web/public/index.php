<?php
include '../app/vendor/autoload.php';

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;


$app = AppFactory::create();


// very bad bad way to know if our DB connection is working
$app->get('/', function (Request $request, Response $response, $args) {
    $response->getBody()->write("Looks like everything is working...</br></br>");
    $response->getBody()->write("try the DB connection -> <a href='http://localhost:8000/db'>DB Check</a></br></br>");
    $response->getBody()->write("try the Redis connection -> <a href='http://localhost:8000/redis'>Redis Check</a></br></br>");

    return $response;
});

// very bad way to know if our DB connection is working
$app->get('/db', function (Request $request, Response $response, $args) {
    try {
        $dsn = 'mysql:host=mysql;dbname=db;charset=utf8;port=3306';
        $pdo = new PDO($dsn, 'dev', 'devPass');
        $response->getBody()->write("DB connected successfully!</br></br><a href='http://localhost:8000/'>back to index</a>");
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return $response;
});

// check redis
$app->get('/redis', function (Request $request, Response $response, $args) {
    try {
        // Parameters passed using a named array:
        $client = new Predis\Client([
            'scheme' => 'tcp',
            'host'   => 'redis',
            'port'   => 6379,
        ]);
        $client->set('foo', 'bar');
        $value = $client->get('foo');

        $response->getBody()->write("Redis is working! -> value foo: " . $value . "</br></br><a href='http://localhost:8000/'>back to index</a>");
    } catch (PDOException $e) {
        echo $e->getMessage();
    }

    return $response;
});

// make sure you ran all migrations and seeds first using MAKE if not this wont work
$app->get('/db/list', function ($request, $response, $args) {
    try {
        $dsn = 'mysql:host=mysql;dbname=db;charset=utf8;port=3306';
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
