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

        if (!empty($filters['usuario_id'])) {
            $where[] = 'usuario_id = :usuario_id';
            $params['usuario_id'] = (int) $filters['usuario_id'];
        }

        if (!empty($filters['acao'])) {
            $where[] = 'acao = :acao';
            $params['acao'] = $filters['acao'];
        }

        $whereSql = 'WHERE ' . implode(' AND ', $where);

        $countSql = "SELECT COUNT(*)
            FROM historico_usuario
            {$whereSql}";

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        $sql = "SELECT *
            FROM historico_usuario
            {$whereSql}
            ORDER BY data_acao DESC, id DESC
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
