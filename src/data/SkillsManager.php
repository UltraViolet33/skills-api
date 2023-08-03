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

        throw new \Exception('Skill with id' . $id . "was not found");
    }
}

?>