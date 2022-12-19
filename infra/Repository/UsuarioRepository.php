<?php

namespace Infra\Repository;

use Domain\Entities\Usuario\UsuarioEntity;
use Infra\Database\Database;

class UsuarioRepository
{
    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function login(string $email, string $senha)
    {
        $sql = "SELECT * FROM usuarios WHERE email = :email AND senha = :senha";
        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':senha', $senha);
        $stmt->execute();

        return $stmt->fetch();
    }

    public function create(UsuarioEntity $usuario): void
    {
        $sql = "INSERT INTO usuarios (email, senha) VALUES (:email, :senha)";
        $stmt = $this->database->getInstance()->prepare($sql);
        $stmt->bindValue(':email', $usuario->getEmail());
        $stmt->bindValue(':senha', $usuario->getSenha());

        if (!$stmt->execute()) {
            throw new \Exception("Erro ao cadastrar usuário", 500);
        }
    }
}
