<?php

namespace Domain\Usecases\Usuario;

use Domain\Entities\Usuario\UsuarioEntity;
use Domain\Usecases\Interfaces\Usecase;
use Infra\Database\MySQLConnection;
use Infra\Repository\UsuarioRepository;

class CadastrarUsuario implements Usecase
{
    public static function handle($data)
    {
        $entity = new UsuarioEntity();
        $entity->from($data);

        $produtoRepository = new UsuarioRepository(new MySQLConnection);
        return $produtoRepository->create($entity);
    }
}
