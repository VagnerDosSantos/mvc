<?php

namespace Domain\Usecases\Usuario;

use Domain\Usecases\Interfaces\Usecase;
use Infra\Database\MySQLConnection;
use Infra\Repository\UsuarioRepository;

class AutenticarUsuario implements Usecase
{
    public static function handle($data)
    {
        $usuario = new UsuarioRepository(new MySQLConnection);
        $usuario = $usuario->login($data['email'], sha1($data['senha']));

        if (!$usuario) {
            throw new \Exception("Usuário ou senha inválidos", 401);
        }

        $_SESSION['user'] = $usuario['email'];
        $_SESSION['isAdmin'] = $usuario['is_admin'];

        return $usuario;
    }
}
