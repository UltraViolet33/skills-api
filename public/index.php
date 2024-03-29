<?php

declare(strict_types=1);

use App\Data\ActionManager;
use App\Data\SkillsManager;
use App\Data\UserManager;
use App\JsonHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

$app->addBodyParsingMiddleware();
$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);

const PATH_JSON_DATA = __DIR__ . "/../src/data/data.json";

$jsonHandler = new JsonHandler(PATH_JSON_DATA);
$user = new UserManager($jsonHandler);
$skills = new SkillsManager($jsonHandler);
$action = new ActionManager($jsonHandler);


$app->get('/user-data', function (Request $request, Response $response) use ($user) {
    $userData = $user->getData();
    $response->getBody()->write(json_encode($userData));

    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
});


$app->get('/all-skills', function (Request $request, Response $response) use ($skills, $action) {
    $allSkills = $skills->getAll();
    $allActions = $action->getAll();

    foreach ($allSkills as $i => $groupSkill) {
        foreach ($groupSkill as $j => $skill) {
            $skill = (array) $skill;
            $allSkills[$i][$j] = (array) $allSkills[$i][$j];
            $allSkills[$i][$j]["actions"] = [];

            foreach ($allActions as $action) {
                $action = (array) $action;
                if ($skill["id"] === $action["id_skill"]) {
                    $allSkills[$i][$j]["actions"][] = $action;
                }
            }
        }
    }

    $response->getBody()->write(json_encode($allSkills));

    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
});

/**
 * add action to a skill 
 * and upgrade user level
 */
$app->post('/actions/add', function (Request $request, Response $response, array $args) use ($app, $action, $skills, $user) {

    $postData = ["id_skill", "name", "level"];
    $data = $request->getParsedBody();

    foreach ($postData as $value) {
        if (empty($data[$value])) {
            $response->getBody()->write(json_encode(["error" => "Missing fields"]));
            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('content-type', 'application/json')
                ->withHeader('Accept', 'application/json')
                ->withStatus(400);
        }
    }

    try {

        $actionSkill = $skills->getById($data["id_skill"]);
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(["error" => "Id skill is not valid"]));
        return $response
            ->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }

    $newAction = [];

    $newAction = [
        "id" => uniqid(),
        "id_skill" => $data["id_skill"],
        "name" => $data["name"],
        "level" => (int) $data["level"],
        "timestamp" => time()
    ];

    $action->addNewAction($newAction);

    $nextSkill = floor($actionSkill["level"] + 1);
    $newSkill = $skills->updateLevel($actionSkill["id"], (int) $data["level"]);

    if ($newSkill["level"] >= $nextSkill) {
        $user->upgradeLevel();
    }

    $response->getBody()->write(json_encode($newSkill));
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
});

$app->run();