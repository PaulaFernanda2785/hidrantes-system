<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class HistoricoRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function create(array $data): void
    {
        $stmt = $this->db->prepare('INSERT INTO historico_usuario (data_acao, usuario_id, usuario_nome_snapshot, acao, entidade, referencia_registro, detalhes, ip_origem) VALUES (NOW(), :usuario_id, :usuario_nome_snapshot, :acao, :entidade, :referencia_registro, :detalhes, :ip_origem)');
        $stmt->execute($data);
    }

    public function paginate(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = max(1, $perPage);
        $offset = ($page - 1) * $perPage;

        $where = ['1=1'];
        $params = [];

        if (!empty($filters['usuario_nome'])) {
            $where[] = 'hu.usuario_nome_snapshot LIKE :usuario_nome';
            $params['usuario_nome'] = '%' . trim((string) $filters['usuario_nome']) . '%';
        }

        if (!empty($filters['acao'])) {
            $where[] = 'hu.acao = :acao';
            $params['acao'] = $filters['acao'];
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $countSql = "SELECT COUNT(*)
            FROM historico_usuario hu
            {$whereSql}";

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT
                hu.*,
                h.numero_hidrante AS hidrante_numero_referencia,
                u.nome AS usuario_nome_referencia,
                b.nome AS bairro_nome_referencia,
                mb.nome AS bairro_municipio_referencia
            FROM historico_usuario hu
            LEFT JOIN hidrantes h
                ON hu.entidade = 'hidrantes'
                AND CAST(hu.referencia_registro AS UNSIGNED) = h.id
            LEFT JOIN usuarios u
                ON hu.entidade = 'usuarios'
                AND CAST(hu.referencia_registro AS UNSIGNED) = u.id
            LEFT JOIN bairros b
                ON hu.entidade = 'bairros'
                AND CAST(hu.referencia_registro AS UNSIGNED) = b.id
            LEFT JOIN municipios mb
                ON b.municipio_id = mb.id
            {$whereSql}
            ORDER BY hu.data_acao DESC, hu.id DESC
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
}
