<?php

declare(strict_types=1);

namespace App\Data;

use App\JsonHandler;

class UserManager
{
    public function __construct(public JsonHandler $jsonHandler)
    {
    }


    public function getData(): array
    {
        $data = $this->jsonHandler->getData();
        return (array) $data["user"];
    }


    public function upgradeLevel()
    {
        $data = $this->getData();
        $data["level"] += 0.2;
        $data["level"] = round($data["level"], 1);

        $this->jsonHandler->writeData("user", $data);
        return $data["level"];
    }
}

?>