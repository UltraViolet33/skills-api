<?php

const COMPULSORY_SKILLS = ["nutrition", "physical activity"];

const BASIC_SKILLS = [
    "history",
    "literature",
    "cinema",
    "decision making",
    "critical thinking",
    "teamwork",
    "real-world sociability",
    "virtual sociability",
    "public speaking"
];

const PERSONAL_SKILLS = [
    "computer literacy",
    "ambition",
    "daring",
    "courage",
    "creative",
    "optimism",
    "perseverance"
];

const USERNAME = "UltraViolet33";

const INITIAL_LEVEL = 0;

const INITIAL_SKILL_LEVEL = 15;

const INITIAL_ACTIONS = [];

$action_example = [
    "id" => uniqid(),
    "id_skill" => 1,
    "name" => "run 1 hour",
    "date" => "22:10:2023",
    "level" => 5
];

$data = [];

$data["user"] = [
    "username" => USERNAME,
    "level" => INITIAL_LEVEL
];

$skills = [];

$skills["compulsory"] = array_map("return_array_skill", COMPULSORY_SKILLS);

$skills["basic"] = array_map("return_array_skill", BASIC_SKILLS);

$skills["personal"] = array_map("return_array_skill", PERSONAL_SKILLS);

$data["skills"] = $skills;

$data["actions"] = INITIAL_ACTIONS;

// $data["actions"][] = $action_example;


function return_array_skill(string $skillName): array
{

    return ["id" => uniqid(), "name" => $skillName, "level" => INITIAL_SKILL_LEVEL];
}


$fp = fopen("./data.json", 'w');
fwrite($fp, json_encode($data));
fclose($fp);

?>


<pre>
    <?= json_encode($data) ?>
</pre>