<?php

namespace App\Repositories;

use App\Core\Database;
use PDO;

class MunicipioRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::connection();
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, nome FROM municipios WHERE ativo = 1 ORDER BY nome ASC')->fetchAll();
    }
}
