<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class HidranteRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function metrics(): array
    {
        $sql = "SELECT 
            COUNT(*) AS total,
            SUM(CASE WHEN status_operacional = 'operante' THEN 1 ELSE 0 END) AS operantes,
            SUM(CASE WHEN status_operacional = 'operante com restricao' THEN 1 ELSE 0 END) AS restricao,
            SUM(CASE WHEN status_operacional = 'inoperante' THEN 1 ELSE 0 END) AS inoperantes
        FROM hidrantes
        WHERE deleted_at IS NULL";

        return $this->db->query($sql)->fetch() ?: [
            'total' => 0,
            'operantes' => 0,
            'restricao' => 0,
            'inoperantes' => 0,
        ];
    }

    public function paginate(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $where = ["h.deleted_at IS NULL"];
        $params = [];

        if (!empty($filters['status_operacional'])) {
            $where[] = 'h.status_operacional = :status_operacional';
            $params['status_operacional'] = $filters['status_operacional'];
        }

        if (!empty($filters['municipio_id'])) {
            $where[] = 'h.municipio_id = :municipio_id';
            $params['municipio_id'] = (int) $filters['municipio_id'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(h.numero_hidrante LIKE :q OR h.endereco LIKE :q OR h.equipe_responsavel LIKE :q)';
            $params['q'] = '%' . trim($filters['q']) . '%';
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $countSql = "SELECT COUNT(*)
                    FROM hidrantes h
                    {$whereSql}";

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT 
                    h.id,
                    h.numero_hidrante,
                    h.status_operacional,
                    h.tipo_hidrante,
                    h.area,
                    h.endereco,
                    h.atualizado_em,
                    m.nome AS municipio_nome,
                    b.nome AS bairro_nome
                FROM hidrantes h
                INNER JOIN municipios m ON m.id = h.municipio_id
                LEFT JOIN bairros b ON b.id = h.bairro_id
                {$whereSql}
                ORDER BY h.atualizado_em DESC, h.id DESC
                LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }

        $stmt->bindValue(':limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, \PDO::PARAM_INT);
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

    public function findById(int $id): ?array
    {
        $sql = "SELECT *
                FROM hidrantes
                WHERE id = :id
                  AND deleted_at IS NULL
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);

        $item = $stmt->fetch();
        return $item ?: null;
    }

    public function mapPoints(): array
    {
        $sql = "SELECT 
                    h.id,
                    h.numero_hidrante,
                    h.status_operacional,
                    h.tipo_hidrante,
                    h.area,
                    h.endereco,
                    h.atualizado_em,
                    h.latitude,
                    h.longitude
                FROM hidrantes h
                WHERE h.deleted_at IS NULL
                  AND h.latitude IS NOT NULL
                  AND h.longitude IS NOT NULL";

        return $this->db->query($sql)->fetchAll();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO hidrantes (
                    numero_hidrante,
                    equipe_responsavel,
                    area,
                    existe_no_local,
                    tipo_hidrante,
                    acessibilidade,
                    tampo_conexoes,
                    tampas_ausentes,
                    caixa_protecao,
                    condicao_caixa,
                    presenca_agua_interior,
                    teste_realizado,
                    resultado_teste,
                    status_operacional,
                    municipio_id,
                    bairro_id,
                    endereco,
                    latitude,
                    longitude,
                    foto_01,
                    foto_02,
                    foto_03,
                    criado_em,
                    atualizado_em,
                    criado_por_usuario_id,
                    atualizado_por_usuario_id
                ) VALUES (
                    :numero_hidrante,
                    :equipe_responsavel,
                    :area,
                    :existe_no_local,
                    :tipo_hidrante,
                    :acessibilidade,
                    :tampo_conexoes,
                    :tampas_ausentes,
                    :caixa_protecao,
                    :condicao_caixa,
                    :presenca_agua_interior,
                    :teste_realizado,
                    :resultado_teste,
                    :status_operacional,
                    :municipio_id,
                    :bairro_id,
                    :endereco,
                    :latitude,
                    :longitude,
                    :foto_01,
                    :foto_02,
                    :foto_03,
                    NOW(),
                    NOW(),
                    :criado_por_usuario_id,
                    :atualizado_por_usuario_id
                )";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $sql = "UPDATE hidrantes SET
                    numero_hidrante = :numero_hidrante,
                    equipe_responsavel = :equipe_responsavel,
                    area = :area,
                    existe_no_local = :existe_no_local,
                    tipo_hidrante = :tipo_hidrante,
                    acessibilidade = :acessibilidade,
                    tampo_conexoes = :tampo_conexoes,
                    tampas_ausentes = :tampas_ausentes,
                    caixa_protecao = :caixa_protecao,
                    condicao_caixa = :condicao_caixa,
                    presenca_agua_interior = :presenca_agua_interior,
                    teste_realizado = :teste_realizado,
                    resultado_teste = :resultado_teste,
                    status_operacional = :status_operacional,
                    municipio_id = :municipio_id,
                    bairro_id = :bairro_id,
                    endereco = :endereco,
                    latitude = :latitude,
                    longitude = :longitude,
                    foto_01 = :foto_01,
                    foto_02 = :foto_02,
                    foto_03 = :foto_03,
                    atualizado_em = NOW(),
                    atualizado_por_usuario_id = :atualizado_por_usuario_id
                WHERE id = :id
                  AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'numero_hidrante' => $data['numero_hidrante'],
            'equipe_responsavel' => $data['equipe_responsavel'],
            'area' => $data['area'],
            'existe_no_local' => $data['existe_no_local'],
            'tipo_hidrante' => $data['tipo_hidrante'],
            'acessibilidade' => $data['acessibilidade'],
            'tampo_conexoes' => $data['tampo_conexoes'],
            'tampas_ausentes' => $data['tampas_ausentes'],
            'caixa_protecao' => $data['caixa_protecao'],
            'condicao_caixa' => $data['condicao_caixa'],
            'presenca_agua_interior' => $data['presenca_agua_interior'],
            'teste_realizado' => $data['teste_realizado'],
            'resultado_teste' => $data['resultado_teste'],
            'status_operacional' => $data['status_operacional'],
            'municipio_id' => $data['municipio_id'],
            'bairro_id' => $data['bairro_id'],
            'endereco' => $data['endereco'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'foto_01' => $data['foto_01'],
            'foto_02' => $data['foto_02'],
            'foto_03' => $data['foto_03'],
            'atualizado_por_usuario_id' => $data['atualizado_por_usuario_id'],
        ]);
    }

   public function softDelete(int $id, int $usuarioId): void
    {
        $sql = "UPDATE hidrantes
                SET deleted_at = NOW(),
                    deleted_por_usuario_id = :deleted_por_usuario_id,
                    atualizado_em = NOW(),
                    atualizado_por_usuario_id = :atualizado_por_usuario_id
                WHERE id = :id
                AND deleted_at IS NULL";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id,
            'deleted_por_usuario_id' => $usuarioId,
            'atualizado_por_usuario_id' => $usuarioId,
        ]);
    }

    public function existsByNumero(string $numero, ?int $ignoreId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM hidrantes WHERE numero_hidrante = :numero';
        $params = ['numero' => $numero];

        if ($ignoreId !== null) {
            $sql .= ' AND id <> :ignore_id';
            $params['ignore_id'] = $ignoreId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn() > 0;
    }

    public function report(array $filters = []): array
    {
        $where = ["h.deleted_at IS NULL"];
        $params = [];

        if (!empty($filters['status_operacional'])) {
            $where[] = 'h.status_operacional = :status_operacional';
            $params['status_operacional'] = $filters['status_operacional'];
        }

        if (!empty($filters['municipio_id'])) {
            $where[] = 'h.municipio_id = :municipio_id';
            $params['municipio_id'] = (int) $filters['municipio_id'];
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $sql = "SELECT
                    h.id,
                    h.numero_hidrante,
                    h.status_operacional,
                    h.tipo_hidrante,
                    h.area,
                    h.endereco,
                    h.atualizado_em,
                    m.nome AS municipio_nome,
                    b.nome AS bairro_nome
                FROM hidrantes h
                INNER JOIN municipios m ON m.id = h.municipio_id
                LEFT JOIN bairros b ON b.id = h.bairro_id
                {$whereSql}
                ORDER BY h.atualizado_em DESC, h.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
}

