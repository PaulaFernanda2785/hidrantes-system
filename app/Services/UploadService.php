<?php

namespace App\Services;

class UploadService
{
    private array $allowedMimeTypes = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    private int $maxFileSize = 5242880;
    private int $maxImageWidth = 8000;
    private int $maxImageHeight = 8000;
    private int $maxPixels = 40000000;

    public function storeMultiple(array $files): array
    {
        $stored = ['foto_01' => null, 'foto_02' => null, 'foto_03' => null];
        if (empty($files['name']) || !is_array($files['name'])) {
            return $stored;
        }

        $directory = storage_path('uploads/hidrantes');
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        for ($i = 0; $i < min(3, count($files['name'])); $i++) {
            if (($files['error'][$i] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
                continue;
            }

            $tmp = $files['tmp_name'][$i];
            $size = (int) ($files['size'][$i] ?? 0);

            if (!is_uploaded_file($tmp) || $size <= 0 || $size > $this->maxFileSize) {
                continue;
            }

            $imageInfo = @getimagesize($tmp);
            if ($imageInfo === false) {
                continue;
            }

            [$width, $height] = $imageInfo;
            if (
                $width <= 0
                || $height <= 0
                || $width > $this->maxImageWidth
                || $height > $this->maxImageHeight
                || ($width * $height) > $this->maxPixels
            ) {
                continue;
            }

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mime = (string) $finfo->file($tmp);
            $extension = $this->allowedMimeTypes[$mime] ?? null;
            if ($extension === null) {
                continue;
            }

            $newName = 'hidrante_' . bin2hex(random_bytes(16)) . '.' . $extension;
            $destination = $directory . DIRECTORY_SEPARATOR . $newName;

            if (move_uploaded_file($tmp, $destination)) {
                @chmod($destination, 0640);
                $stored['foto_0' . ($i + 1)] = $newName;
            }
        }

        return $stored;
    }
}
