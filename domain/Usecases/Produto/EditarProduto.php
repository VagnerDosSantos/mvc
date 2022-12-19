<?php

namespace Domain\Usecases\Produto;

use Domain\Entities\Produto\ProdutoEntity;
use Domain\Usecases\Interfaces\Usecase;
use Infra\Database\MySQLConnection;
use Infra\Repository\ProdutoRepository;

class EditarProduto implements Usecase
{
    public static function handle($data)
    {
        $entity = new ProdutoEntity($data);
        $entity->from($data);
        $entity->setId($data['id']);

        $produtoRepository = new ProdutoRepository(new MySQLConnection);
        return $produtoRepository->update($entity);
    }
}
