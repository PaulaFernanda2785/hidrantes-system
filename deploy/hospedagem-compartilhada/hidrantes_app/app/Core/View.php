<?php

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], string $layout = 'layouts/master'): void
    {
        $viewFile = base_path('resources/views/' . $view . '.php');
        $layoutFile = base_path('resources/views/' . $layout . '.php');

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View não encontrada: {$view}");
        }

        extract($data);
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        require $layoutFile;
    }
}
