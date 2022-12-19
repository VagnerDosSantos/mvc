<?php

namespace Infra\Core\Request;

class Response
{
    public static function json($data, $code = 200)
    {
        header('Content-Type: application/json');
        http_response_code($code);

        echo json_encode($data);
    }

    public static function noContent(): void
    {
        http_response_code(204);
    }
}
