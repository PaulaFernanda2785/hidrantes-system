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

    public function all(array $filters = []): array
    {
        $sql = 'SELECT * FROM historico_usuario WHERE 1=1';
        $params = [];
        if (!empty($filters['usuario_id'])) {
            $sql .= ' AND usuario_id = :usuario_id';
            $params['usuario_id'] = $filters['usuario_id'];
        }
        if (!empty($filters['acao'])) {
            $sql .= ' AND acao = :acao';
            $params['acao'] = $filters['acao'];
        }
        $sql .= ' ORDER BY data_acao DESC LIMIT 200';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
