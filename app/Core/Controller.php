<?php

namespace App\Core;

abstract class Controller
{
    protected Request $request;

    public function __construct()
    {
        $this->request = new Request();
    }

    protected function view(string $view, array $data = [], string $layout = 'layouts/master'): void
    {
        View::render($view, $data, $layout);
    }

    protected function redirect(string $path, ?string $success = null, ?string $error = null): void
    {
        if ($success) {
            Session::flash('success', $success);
        }
        if ($error) {
            Session::flash('error', $error);
        }
        redirect($path);
    }
}
