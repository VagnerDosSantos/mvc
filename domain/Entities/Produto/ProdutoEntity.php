<?php

namespace Domain\Entities\Produto;

use Domain\Entities\Interfaces\Entity;
use Infra\Core\Request\Validate;

class ProdutoEntity implements Entity
{
    private int $id;
    private string $nome;
    private float $preco;
    private int $quantidade;
    private ?string $descricao = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getNome(): string
    {
        return $this->nome;
    }

    public function getPreco(): float
    {
        return $this->preco;
    }

    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    public function getDescricao(): ?string
    {
        return $this->descricao;
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
            'preco' => 'required|numeric|greater_than:0',
            'quantidade' => 'required|integer|min:0',
            'descricao' => 'nullable|min_digits:10|max_digits:255'
        ]);

        $this->nome = $validated['nome'];
        $this->preco = $validated['preco'];
        $this->quantidade = $validated['quantidade'];
        $this->descricao = $validated['descricao'];
    }
}
