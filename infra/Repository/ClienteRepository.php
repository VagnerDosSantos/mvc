<?php

namespace Infra\Repository;

use Domain\Entities\Cliente\ClienteEntity;
use Infra\Database\Database;

class ClienteRepository
{
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function paginate(int $pagina = 1, int $limite = 20)
    {
        $sql = 'SELECT COUNT(id) FROM clientes WHERE ativo = :ativo';
        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':ativo', 1, \PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchColumn();
        $pagesQuantity = ceil($items / $limite);

        $offset = ($pagina - 1) * $limite;

        $sql = 'SELECT * FROM clientes WHERE ativo = :ativo LIMIT :limit OFFSET :offset';

        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':ativo', 1, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limite, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return [
            'clients' => $stmt->fetchAll(),
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
        $sql = 'SELECT * FROM clientes WHERE id = :id';

        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function all()
    {
        $sql = 'SELECT * FROM clientes WHERE ativo = :ativo';

        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':ativo', 1, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function getClientByCpf(string $cpf): array|bool
    {
        $sql = 'SELECT * FROM clientes WHERE cpf = :cpf AND ativo = :ativo';

        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':cpf', $cpf, \PDO::PARAM_STR);
        $stmt->bindValue(':ativo', 1, \PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function create(ClienteEntity $cliente): bool
    {
        $clienteCadastrado = $this->getClientByCpf($cliente->getCpf());

        if ($clienteCadastrado['cpf'] == $cliente->getCpf()) {
            throw new \Exception('Cliente já cadastrado', 400);
        }

        $sql = 'INSERT INTO clientes (nome, data_nascimento, cpf) VALUES (:nome, :data_nascimento, :cpf)';

        $stmt = $this->database->getInstance()->prepare($sql);

        $stmt->bindValue(':nome', $cliente->getNome(), \PDO::PARAM_STR);
        $stmt->bindValue(':data_nascimento', $cliente->getDataNascimento(), \PDO::PARAM_STR);
        $stmt->bindValue(':cpf', $cliente->getCpf(), \PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function update(ClienteEntity $cliente): bool
    {
        $existeCliente = $this->find($cliente->getId());

        if (!$existeCliente) {
            throw new \Exception('Cliente não encontrado', 404);
        }

        $clienteCadastrado = $this->getClientByCpf($cliente->getCpf());

        if ($clienteCadastrado['cpf'] == $cliente->getCpf() && $clienteCadastrado['id'] != $cliente->getId()) {
            throw new \Exception('Já existe um cliente cadastrado com este CNPJ', 400);
        }

        $sql = 'UPDATE clientes SET nome = :nome, data_nascimento = :data_nascimento, cpf = :cpf WHERE id = :id';
        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':id', $cliente->getId(), \PDO::PARAM_INT);
        $stmt->bindValue(':nome', $cliente->getNome(), \PDO::PARAM_STR);
        $stmt->bindValue(':data_nascimento', $cliente->getDataNascimento(), \PDO::PARAM_STR);
        $stmt->bindValue(':cpf', $cliente->getCpf(), \PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $existeCliente = $this->find($id);

        if (!$existeCliente) {
            throw new \Exception('Cliente não encontrado', 404);
        }

        $sql = 'UPDATE clientes SET ativo = :ativo WHERE id = :id';
        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':id', $id, \PDO::PARAM_INT);
        $stmt->bindValue(':ativo', 0, \PDO::PARAM_INT);

        return $stmt->execute();
    }
}
