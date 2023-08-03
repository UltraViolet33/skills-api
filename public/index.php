<?php

use App\Data\SkillsManager;
use App\Data\UserManager;
use App\JsonHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);


const PATH_JSON_DATA = __DIR__ . "/../src/data/data.json";

$jsonHandler = new JsonHandler(PATH_JSON_DATA);

$user = new UserManager($jsonHandler);

$skills = new SkillsManager($jsonHandler);


$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write('Hello World!');
    return $response;
});

$app->get('/user-data', function (Request $request, Response $response) use ($user) {

    $userData = $user->getData();
    $response->getBody()->write(json_encode($userData));

    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);

});


$app->get('/all-skills', function (Request $request, Response $response) use ($skills) {

    $allSkills = $skills->getAll();
    $response->getBody()->write(json_encode($allSkills));
    
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);

});


$app->get('/specific-skills', function (Request $request, Response $response) use ($skills, $app) {


    $params = $request->getQueryParams();
    $category = $params["cat"];
    
    $skillsData = $skills->getSpecificSkills($category);
    $response->getBody()->write(json_encode($skillsData));
    
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);

});

$app->post('/user-data/add', function (Request $request, Response $response, array $args) {

    $data = $request->getParsedBody();
    $name = $data["name"];
    $level = $data["level"];


    try {

        $result = ["msg" => "ok", "user" => ["name" => $name, "level" => $level]];
        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

$app->run();