<?php

namespace Infra\Repository;

use Domain\Entities\Produto\ProdutoEntity;
use Infra\Database\Database;

class ProdutoRepository
{
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function paginate(int $pagina = 1, int $limite = 20)
    {
        $sql = 'SELECT COUNT(id) FROM produtos WHERE ativo = :ativo';
        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':ativo', 1, \PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchColumn();
        $pagesQuantity = ceil($items / $limite);

        $offset = ($pagina - 1) * $limite;

        $sql = 'SELECT * FROM produtos WHERE ativo = :ativo LIMIT :limit OFFSET :offset';

        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':ativo', 1, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limite, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'products' => $stmt->fetchAll(),
            'pagination' => [
                'currentPage' => $pagina,
                'nextPage' => $pagina + 1,
                'previousPage' => $pagina - 1,
                'lastPage' => $pagesQuantity,
                'itemsPerPage' => $limite
            ]
        ];
    }

    public function find(int $id): array|bool
    {
        $sql = 'SELECT * FROM produtos WHERE id = :id';

        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function all()
    {
        $sql = 'SELECT * FROM produtos WHERE ativo = :ativo';

        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':ativo', 1, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function create(ProdutoEntity $produto): bool
    {
        $sql = 'INSERT INTO produtos (nome, descricao, preco, quantidade) VALUES (:nome, :descricao, :preco, :quantidade)';

        $stmt = $this->database->getInstance()->prepare($sql);

        $stmt->bindValue(':nome', $produto->getNome(), \PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $produto->getDescricao(), \PDO::PARAM_STR);
        $stmt->bindValue(':preco', $produto->getPreco(), \PDO::PARAM_STR);
        $stmt->bindValue(':quantidade', $produto->getQuantidade(), \PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function update(ProdutoEntity $produto): bool
    {
        $existeProduto = $this->find($produto->getId());

        if (!$existeProduto) {
            throw new \Exception('Produto não encontrado', 404);
        }

        $sql = 'UPDATE produtos SET nome = :nome, descricao = :descricao, preco = :preco, quantidade = :quantidade WHERE id = :id';
        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':id', $produto->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(':nome', $produto->getNome(), \PDO::PARAM_STR);
        $stmt->bindValue(':descricao', $produto->getDescricao(), \PDO::PARAM_STR);
        $stmt->bindValue(':preco', $produto->getPreco(), \PDO::PARAM_STR);
        $stmt->bindValue(':quantidade', $produto->getQuantidade(), \PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $existeProduto = $this->find($id);

        if (!$existeProduto) {
            throw new \Exception('Produto não encontrado', 404);
        }

        $sql = 'UPDATE produtos SET ativo = :ativo WHERE id = :id';
        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':ativo', 0, \PDO::PARAM_INT);

        return $stmt->execute();
    }

    public function updateQuantidade(int $id, int $quantidade): bool
    {
        $sql = 'UPDATE produtos SET quantidade = :quantidade WHERE id = :id';
        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':quantidade', $quantidade, \PDO::PARAM_INT);

        return $stmt->execute();
    }
}
