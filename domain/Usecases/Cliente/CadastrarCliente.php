<?php

namespace Domain\Usecases\Cliente;

use Domain\Entities\Cliente\ClienteEntity;
use Domain\Usecases\Interfaces\Usecase;
use Infra\Database\MySQLConnection;
use Infra\Repository\ClienteRepository;

class CadastrarCliente implements Usecase
{
    public static function handle($data)
    {
        $entity = new ClienteEntity();
        $entity->from($data);

        $produtoRepository = new ClienteRepository(new MySQLConnection);
        return $produtoRepository->create($entity);
    }
}
