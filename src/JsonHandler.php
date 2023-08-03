<?php

declare(strict_types=1);


namespace App;

class JsonHandler
{

    public function __construct(public string $path)
    {
    }


    public function getData(): array
    {
        $dataStr = file_get_contents($this->path);
        return (array) json_decode($dataStr);
    }



}


?>