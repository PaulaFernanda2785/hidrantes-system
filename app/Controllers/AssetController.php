<?php

namespace App\Controllers;

use App\Core\Controller;

class AssetController extends Controller
{
    public function rootCss(string $file): void
    {
        $this->serveAsset('css', $file, 'css', 'text/css; charset=utf-8', 'CSS');
    }

    public function nestedCss(string $directory, string $file): void
    {
        $this->serveAsset('css', $directory . '/' . $file, 'css', 'text/css; charset=utf-8', 'CSS');
    }

    public function nestedJs(string $directory, string $file): void
    {
        $this->serveAsset('js', $directory . '/' . $file, 'js', 'application/javascript; charset=utf-8', 'JS');
    }

    public function deepJs(string $directory, string $subdirectory, string $file): void
    {
        $this->serveAsset('js', $directory . '/' . $subdirectory . '/' . $file, 'js', 'application/javascript; charset=utf-8', 'JS');
    }

    private function serveAsset(
        string $baseDirectory,
        string $relativePath,
        string $extension,
        string $contentType,
        string $label
    ): void {
        $basePath = realpath(base_path('resources/assets/' . $baseDirectory));
        if ($basePath === false) {
            http_response_code(404);
            echo 'Arquivo ' . $label . ' nao encontrado.';
            return;
        }

        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        $candidatePath = $basePath . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $targetPath = realpath($candidatePath);

        if (
            $targetPath === false
            || !is_file($targetPath)
            || pathinfo($targetPath, PATHINFO_EXTENSION) !== $extension
            || ($targetPath !== $basePath && !str_starts_with($targetPath, $basePath . DIRECTORY_SEPARATOR))
        ) {
            http_response_code(404);
            echo 'Arquivo ' . $label . ' nao encontrado.';
            return;
        }

        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . (string) filesize($targetPath));
        header('Cache-Control: public, max-age=3600');
        header('X-Content-Type-Options: nosniff');

        readfile($targetPath);
        exit;
    }
}
