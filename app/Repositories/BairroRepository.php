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
}
