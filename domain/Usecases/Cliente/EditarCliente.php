<?php

namespace Domain\Usecases\Cliente;

use Domain\Entities\Cliente\ClienteEntity;
use Domain\Usecases\Interfaces\Usecase;
use Infra\Database\MySQLConnection;
use Infra\Repository\ClienteRepository;

class EditarCliente implements Usecase
{
    public static function handle($data)
    {
        $entity = new ClienteEntity();
        $entity->from($data);
        $entity->setId($data['id']);

        $produtoRepository = new ClienteRepository(new MySQLConnection);
        return $produtoRepository->update($entity);
    }
}
