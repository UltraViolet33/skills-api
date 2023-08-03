<?php

declare(strict_types=1);

namespace App\Model;


class Skill
{
    public function __construct(
        public string $id,
        public string $name,
        public float $level
    ) {
    }


    public static function fromArray(array $skill): self
    {
        return new self($skill["id"], $skill["name"], $skill["level"]);
    }


    public function toArray(): array
    {
        return ["id" => $this->id, "name" => $this->name, "level" => $this->name];
    }
}



?>