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
}

?>