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
        FROM hidrantes";
        return $this->db->query($sql)->fetch() ?: ['total' => 0, 'operantes' => 0, 'restricao' => 0, 'inoperantes' => 0];
    }

    public function all(array $filters = []): array
    {
        $sql = "SELECT h.id, h.numero_hidrante, h.status_operacional, h.tipo_hidrante, h.area, h.endereco, h.atualizado_em,
                       m.nome AS municipio_nome, b.nome AS bairro_nome
                FROM hidrantes h
                INNER JOIN municipios m ON m.id = h.municipio_id
                LEFT JOIN bairros b ON b.id = h.bairro_id
                WHERE 1=1";
        $params = [];

        if (!empty($filters['status_operacional'])) {
            $sql .= ' AND h.status_operacional = :status_operacional';
            $params['status_operacional'] = $filters['status_operacional'];
        }
        if (!empty($filters['municipio_id'])) {
            $sql .= ' AND h.municipio_id = :municipio_id';
            $params['municipio_id'] = $filters['municipio_id'];
        }

        $sql .= ' ORDER BY h.atualizado_em DESC LIMIT 100';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function mapPoints(): array
    {
        $sql = "SELECT h.id, h.numero_hidrante, h.status_operacional, h.tipo_hidrante, h.area, h.endereco, h.atualizado_em, h.latitude, h.longitude
                FROM hidrantes h
                WHERE h.latitude IS NOT NULL AND h.longitude IS NOT NULL";
        return $this->db->query($sql)->fetchAll();
    }

    public function create(array $data): int
    {
        $sql = "INSERT INTO hidrantes (
                    numero_hidrante, equipe_responsavel, area, existe_no_local, tipo_hidrante, acessibilidade,
                    tampo_conexoes, tampas_ausentes, caixa_protecao, condicao_caixa, presenca_agua_interior,
                    teste_realizado, resultado_teste, status_operacional, municipio_id, bairro_id, endereco,
                    latitude, longitude, foto_01, foto_02, foto_03, criado_em, atualizado_em,
                    criado_por_usuario_id, atualizado_por_usuario_id
                ) VALUES (
                    :numero_hidrante, :equipe_responsavel, :area, :existe_no_local, :tipo_hidrante, :acessibilidade,
                    :tampo_conexoes, :tampas_ausentes, :caixa_protecao, :condicao_caixa, :presenca_agua_interior,
                    :teste_realizado, :resultado_teste, :status_operacional, :municipio_id, :bairro_id, :endereco,
                    :latitude, :longitude, :foto_01, :foto_02, :foto_03, NOW(), NOW(),
                    :criado_por_usuario_id, :atualizado_por_usuario_id
                )";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function existsByNumero(string $numero): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM hidrantes WHERE numero_hidrante = :numero');
        $stmt->execute(['numero' => $numero]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
