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

$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();

$app->add(new BasePathMiddleware($app));

$app->addErrorMiddleware(true, true, true);

const PATH_JSON_DATA = __DIR__ . "/../src/data/data.json";

$jsonHandler = new JsonHandler(PATH_JSON_DATA);

$user = new UserManager($jsonHandler);
$skills = new SkillsManager($jsonHandler);
$action = new ActionManager($jsonHandler);


function checkLastSkillsActions()
{

    // check for the last action of each skill
    // if a skill has no action then do nothing
    // if the last action of a skill is more than 60 days, then downgrade and mark the date

    // if a skill has alredy been downgrade more than 30 days ago and has no new actions then downgrade again

}

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


$app->get('/specific-skills', function (Request $request, Response $response) use ($skills) {
    $params = $request->getQueryParams();
    $category = $params["cat"];

    $skillsData = $skills->getSpecificSkills($category);
    $response->getBody()->write(json_encode($skillsData));

    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);

});

$app->post('/actions/add', function (Request $request, Response $response, array $args) use ($action, $skills, $user) {

    $postData = ["id_skill", "name", "level"];
    $data = $request->getParsedBody();

    foreach ($postData as $value) {
        if (!isset($data[$value]) || empty($data[$value])) {
            $response->getBody()->write(json_encode(["error" => "Missing fields"]));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(400);
        }
    }


    try {

        $actionSkill = $skills->getById($data["id_skill"]);
    } catch (Exception $e) {
        $response->getBody()->write(json_encode(["error" => "Id skill is not valid"]));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);

    }

    $newAction = [];

    $newAction = [
        "id" => uniqid(),
        "id_skill" => $data["id_skill"],
        "name" => $data["name"],
        "level" => $data["level"],
        "timestamp" => time()
    ];

    $action->addNewAction($newAction);

    // add level skill

    $nextSkill = floor($actionSkill["level"] + 1);
    $newSkill = $skills->updateLevel($actionSkill["id"], $data["level"]);

    if ($newSkill["level"] >= $nextSkill) {
        $user->upgradeLevel();
    }

    $response->getBody()->write(json_encode($newSkill));
    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
});


$app->get('/all-actions', function (Request $request, Response $response) use ($action) {
    $allActions = $action->getAll();
    $response->getBody()->write(json_encode($allActions));

    return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);
});

$app->run();