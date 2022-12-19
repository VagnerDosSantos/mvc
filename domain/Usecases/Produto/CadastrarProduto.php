<?php

namespace Domain\Usecases\Produto;

use Domain\Entities\Produto\ProdutoEntity;
use Domain\Usecases\Interfaces\Usecase;
use Infra\Database\MySQLConnection;
use Infra\Repository\ProdutoRepository;

class CadastrarProduto implements Usecase
{
    public static function handle($data)
    {
        $entity = new ProdutoEntity();
        $entity->from($data);

        $produtoRepository = new ProdutoRepository(new MySQLConnection);
        return $produtoRepository->create($entity);
    }
}
