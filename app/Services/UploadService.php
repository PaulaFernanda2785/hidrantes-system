<?php

namespace App\Services;

class UploadService
{
    private array $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

    public function storeMultiple(array $files): array
    {
        $stored = ['foto_01' => null, 'foto_02' => null, 'foto_03' => null];
        if (empty($files['name']) || !is_array($files['name'])) {
            return $stored;
        }

        for ($i = 0; $i < min(3, count($files['name'])); $i++) {
            if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                continue;
            }

            $name = $files['name'][$i];
            $tmp = $files['tmp_name'][$i];
            $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (!in_array($ext, $this->allowedExtensions, true)) {
                continue;
            }

            $newName = uniqid('hidrante_', true) . '.' . $ext;
            $destination = storage_path('uploads/hidrantes/' . $newName);
            if (move_uploaded_file($tmp, $destination)) {
                $stored['foto_0' . ($i + 1)] = $newName;
            }
        }

        return $stored;
    }
}
