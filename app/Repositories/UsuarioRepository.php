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

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function existsByMatricula(string $matricula, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM usuarios WHERE matricula_funcional = :matricula';
        $params = ['matricula' => $matricula];

        if ($ignoreId !== null) {
            $sql .= ' AND id <> :ignore_id';
            $params['ignore_id'] = $ignoreId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function metrics(): array
    {
        $sql = "SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN perfil = 'admin' THEN 1 ELSE 0 END) AS admins,
                SUM(CASE WHEN perfil = 'gestor' THEN 1 ELSE 0 END) AS gestores,
                SUM(CASE WHEN perfil = 'operador' THEN 1 ELSE 0 END) AS operadores
            FROM usuarios";

        return $this->db->query($sql)->fetch() ?: [
            'total' => 0,
            'admins' => 0,
            'gestores' => 0,
            'operadores' => 0,
        ];
    }

    public function paginate(?string $nome = null, int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $where = [];
        $params = [];

        if ($nome !== null && trim($nome) !== '') {
            $where[] = 'nome LIKE :nome';
            $params['nome'] = '%' . trim($nome) . '%';
        }

        $whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

        $countSql = "SELECT COUNT(*)
            FROM usuarios
            {$whereSql}";

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT
                id,
                nome,
                matricula_funcional,
                perfil,
                status,
                criado_em,
                atualizado_em
            FROM usuarios
            {$whereSql}
            ORDER BY nome ASC, id ASC
            LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        $items = $stmt->fetchAll();
        $lastPage = max(1, (int) ceil($total / $perPage));

        return [
            'data' => $items,
            'meta' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => $lastPage,
                'from' => $total > 0 ? $offset + 1 : 0,
                'to' => min($offset + $perPage, $total),
            ],
        ];
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

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET nome = :nome, matricula_funcional = :matricula_funcional, perfil = :perfil, status = :status, atualizado_em = NOW() WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'nome' => $data['nome'],
            'matricula_funcional' => $data['matricula_funcional'],
            'perfil' => $data['perfil'],
            'status' => $data['status'],
        ]);
    }

    public function updatePassword(int $id, string $senhaHash): void
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET senha_hash = :senha_hash, atualizado_em = NOW() WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'senha_hash' => $senhaHash,
        ]);
    }

    public function updateStatus(int $id, string $status): void
    {
        $stmt = $this->db->prepare('UPDATE usuarios SET status = :status, atualizado_em = NOW() WHERE id = :id');
        $stmt->execute([
            'id' => $id,
            'status' => $status,
        ]);
    }
}
