<?php

declare(strict_types=1);

namespace App\Data;

use App\JsonHandler;

class ActionManager
{
    public function __construct(public JsonHandler $jsonHandler)
    {
    }


    public function getAll(): array
    {
        $data = $this->jsonHandler->getData();
        return (array) $data["actions"];
    }


    public function addNewAction(array $action): void
    {
        $allActions = $this->getAll();

        $allActions[] = $action;

        $this->jsonHandler->writeData("actions", $allActions);
    }
}

?>