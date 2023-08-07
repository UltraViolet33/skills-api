<?php

declare(strict_types=1);

namespace App\Data;

use App\JsonHandler;

class SkillsManager
{
    public function __construct(public JsonHandler $jsonHandler)
    {
    }


    public function getAll(): array
    {
        $data = $this->jsonHandler->getData();
        return (array) $data["skills"];
    }


    public function getSpecificSkills(string $skills): array
    {
        $data = $this->jsonHandler->getData();
        $allSkills = (array) $data["skills"];

        return $allSkills[$skills];
    }


    public function getById(string $id): array
    {
        $allSkills = $this->getAll();

        foreach ($allSkills as $skillCategory) {
            foreach ($skillCategory as $skill) {
                $skill = (array) $skill;
                if ($skill["id"] === $id) {
                    return $skill;
                }
            }
        }

        // throw new \Exception('Skill with id' . $id . "was not found");
    }


    public function editSkill(string $idSkill, array $newSkill)
    {
        $allSkills = $this->getAll();

        foreach ($allSkills as $i => $skillCategory) {
            foreach ($skillCategory as $j => $skill) {
                $skill = (array) $skill;
                if ($skill["id"] === $idSkill) {
                    // $skill = $newSkill;
                    $allSkills[$i][$j] = $newSkill;
                }
            }
        }
        // throw new \Exception('Skill with id' . $id . "was not found");

        $this->jsonHandler->writeData("skills", $allSkills);
    }


    public function updateLevel(string $idSkill, int $level): array
    {
        $skill = $this->getById($idSkill);
        $value = 0.2 * $level;
        $skill["level"] = round($value + $skill["level"], 2);

        $this->editSkill($idSkill, $skill);
        return $skill;
    }
}

?>