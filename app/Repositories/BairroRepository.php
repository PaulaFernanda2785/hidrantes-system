<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class BairroRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function byMunicipio(int $municipioId): array
    {
        $stmt = $this->db->prepare('SELECT id, nome FROM bairros WHERE municipio_id = :municipio_id AND ativo = 1 ORDER BY nome ASC');
        $stmt->execute(['municipio_id' => $municipioId]);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT
                b.id,
                b.municipio_id,
                b.nome,
                b.ativo,
                m.nome AS municipio_nome
             FROM bairros b
             INNER JOIN municipios m ON m.id = b.municipio_id
             WHERE b.id = :id
             LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        $bairro = $stmt->fetch();

        return $bairro ?: null;
    }

    public function findByMunicipioAndNome(int $municipioId, string $nome, ?int $ignoreId = null): ?array
    {
        $sql = 'SELECT id, municipio_id, nome, ativo
             FROM bairros
             WHERE municipio_id = :municipio_id
               AND LOWER(nome) = LOWER(:nome)';

        $params = [
            'municipio_id' => $municipioId,
            'nome' => $nome,
        ];

        if ($ignoreId !== null) {
            $sql .= ' AND id <> :ignore_id';
            $params['ignore_id'] = $ignoreId;
        }

        $sql .= ' LIMIT 1';

        $stmt = $this->db->prepare(
            $sql
        );
        $stmt->execute($params);

        $bairro = $stmt->fetch();

        return $bairro ?: null;
    }

    public function belongsToMunicipio(int $bairroId, int $municipioId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*)
             FROM bairros
             WHERE id = :id
               AND municipio_id = :municipio_id
               AND ativo = 1'
        );
        $stmt->execute([
            'id' => $bairroId,
            'municipio_id' => $municipioId,
        ]);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO bairros (municipio_id, nome, ativo)
             VALUES (:municipio_id, :nome, 1)'
        );
        $stmt->execute([
            'municipio_id' => $data['municipio_id'],
            'nome' => $data['nome'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function reactivate(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE bairros SET ativo = 1 WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function update(int $id, array $data): void
    {
        $stmt = $this->db->prepare(
            'UPDATE bairros
             SET nome = :nome
             WHERE id = :id'
        );
        $stmt->execute([
            'id' => $id,
            'nome' => $data['nome'],
        ]);
    }
}
