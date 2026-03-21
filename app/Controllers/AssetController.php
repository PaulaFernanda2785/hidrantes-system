<?php

namespace App\Controllers;

use App\Core\Controller;

class AssetController extends Controller
{
    public function rootCss(string $file): void
    {
        $this->serveCss($file);
    }

    public function nestedCss(string $directory, string $file): void
    {
        $this->serveCss($directory . '/' . $file);
    }

    private function serveCss(string $relativePath): void
    {
        $basePath = realpath(base_path('resources/assets/css'));
        if ($basePath === false) {
            http_response_code(404);
            echo 'Arquivo CSS nao encontrado.';
            return;
        }

        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        $candidatePath = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $targetPath = realpath($candidatePath);

        if (
            $targetPath === false
            || !is_file($targetPath)
            || pathinfo($targetPath, PATHINFO_EXTENSION) !== 'css'
            || ($targetPath !== $basePath && !str_starts_with($targetPath, $basePath . DIRECTORY_SEPARATOR))
        ) {
            http_response_code(404);
            echo 'Arquivo CSS nao encontrado.';
            return;
        }

        header('Content-Type: text/css; charset=utf-8');
        header('Content-Length: ' . (string) filesize($targetPath));
        header('Cache-Control: public, max-age=3600');
        header('X-Content-Type-Options: nosniff');

        readfile($targetPath);
        exit;
    }
}
