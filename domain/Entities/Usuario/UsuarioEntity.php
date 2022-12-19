<?php

namespace Domain\Entities\Usuario;

use Domain\Entities\Interfaces\Entity;
use Infra\Core\Request\Validate;

class UsuarioEntity implements Entity
{
    private int $id;
    private string $email;
    private float $senha;

    public function getId(): int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getSenha(): string
    {
        return sha1($this->senha);
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
            'email' => 'required|email',
            'senha' => 'required|min_digits:6',
        ]);

        $this->email = $validated['email'];
        $this->senha = $validated['senha'];
    }
}
