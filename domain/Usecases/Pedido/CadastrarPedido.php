<?php

namespace Domain\Usecases\Pedido;

use Domain\Entities\Pedido\PedidoEntity;
use Domain\Usecases\Interfaces\Usecase;
use Infra\Database\MySQLConnection;
use Infra\Repository\PedidoRepository;

class CadastrarPedido implements Usecase
{
    public static function handle($data)
    {
        $entity = new PedidoEntity();
        $entity->from($data);

        $pedidoRepository = new PedidoRepository(new MySQLConnection);
        return $pedidoRepository->create($entity);
    }
}
