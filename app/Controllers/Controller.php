<?php

namespace App\Controllers;

use Infra\Core\Request\Request;

abstract class Controller
{
    protected Request $request;

    public function __construct()
    {
        $array = json_decode(file_get_contents("php://input"), true);

        foreach ($array ?? [] as $key => $value) {
            $_REQUEST[$key] = $value;
        }

        $this->request = new Request($_REQUEST);
    }
}
