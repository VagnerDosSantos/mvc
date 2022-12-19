<?php

namespace Domain\Entities\Cliente;

use Domain\Entities\Interfaces\Entity;
use Infra\Core\Request\Validate;

class ClienteEntity implements Entity
{
    private int $id;
    private string $nome;
    private string $data_nascimento;
    private string $cpf;

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getDataNascimento(): string
    {
        return $this->data_nascimento;
    }

    public function getCpf(): string
    {
        return $this->cpf;
    }

    public function setId($id)
    {
        $validated = Validate::handle(
            [
                'id' => $id
            ],
            [
                'id' => 'required|greater_than:0'
            ]
        );

        $this->id = $validated['id'];
    }

    public function from(array $data)
    {
        $validated = Validate::handle($data, [
            'nome' => 'required|min_digits:2|max_digits:100',
            'data_nascimento' => 'required|date',
            'cpf' => 'required|cpf'
        ]);

        $this->nome = $validated['nome'];
        $this->data_nascimento = $validated['data_nascimento'];
        $this->cpf = $validated['cpf'];
    }
}
