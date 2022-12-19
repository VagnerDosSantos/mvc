<?php

namespace Infra\Core\Request;

class Exception
{
    public static function handle(\Throwable $th)
    {
        $message = $th->getMessage();
        $code = (int) $th->getCode();

        if ($code == 422) {
            return Response::json([
                'mensagem' => 'Ocorreu um erro na validação do formulário!',
                'dados' => json_decode($message),
            ], $code);
        }

        http_response_code($code);

        return Response::json([
            'mensagem' => $message,
        ], $code);
    }
}
