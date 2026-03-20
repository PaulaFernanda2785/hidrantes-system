<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class UsuarioRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function findByMatricula(string $matricula): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE matricula_funcional = :matricula LIMIT 1');
        $stmt->execute(['matricula' => $matricula]);
        return $stmt->fetch() ?: null;
    }

    public function all(?string $nome = null): array
    {
        $sql = 'SELECT id, nome, matricula_funcional, perfil, status, criado_em FROM usuarios';
        $params = [];
        if ($nome) {
            $sql .= ' WHERE nome LIKE :nome';
            $params['nome'] = '%' . $nome . '%';
        }
        $sql .= ' ORDER BY nome ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO usuarios (nome, matricula_funcional, senha_hash, perfil, status, criado_em, atualizado_em) VALUES (:nome, :matricula_funcional, :senha_hash, :perfil, :status, NOW(), NOW())');
        $stmt->execute([
            'nome' => $data['nome'],
            'matricula_funcional' => $data['matricula_funcional'],
            'senha_hash' => $data['senha_hash'],
            'perfil' => $data['perfil'],
            'status' => $data['status'],
        ]);
        return (int) $this->db->lastInsertId();
    }
}
