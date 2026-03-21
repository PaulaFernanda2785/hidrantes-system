<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\BairroRepository;
use App\Repositories\MunicipioRepository;
use App\Services\HidranteService;
use App\Validators\ValidationException;

class HidranteController extends Controller
{
    public function index(): void
    {
        $filters = [
            'q' => trim((string) $this->request->input('q')),
            'status_operacional' => $this->request->input('status_operacional'),
            'municipio_id' => $this->request->input('municipio_id'),
        ];

        $page = (int) ($this->request->input('page') ?: 1);
        $perPage = 15;

        $service = new HidranteService();
        $result = $service->list($filters, $page, $perPage);

        $municipios = (new MunicipioRepository())->all();

        $this->view('hidrantes/index', [
            'title' => 'Hidrantes',
            'hidrantes' => $result['data'],
            'pagination' => $result['meta'],
            'filters' => $filters,
            'municipios' => $municipios,
        ]);
    }

    public function create(): void
    {
        $this->view('hidrantes/create', [
            'title' => 'Novo Hidrante',
            'formAction' => '/hidrantes/salvar',
            'hidrante' => null,
            'municipios' => (new MunicipioRepository())->all(),
            'bairros' => [],
        ]);
    }

    public function store(): void
    {
        $auth = Session::get('auth');

        try {
            (new HidranteService())->create(
                $this->request->all(),
                $this->request->file('fotos') ?? [],
                $auth
            );

            $this->redirect('/hidrantes', 'Hidrante cadastrado com sucesso.');
        } catch (ValidationException $e) {
            $this->redirect('/hidrantes/novo', null, $e->getMessage());
        } catch (\Throwable $e) {
            report_exception($e);
            $this->redirect('/hidrantes/novo', null, 'Nao foi possivel salvar o hidrante agora.');
        }
    }

    public function edit(string $id): void
    {
        $service = new HidranteService();
        $hidrante = $service->find((int) $id);

        if (!$hidrante) {
            $this->redirect('/hidrantes', null, 'Hidrante nao encontrado.');
        }

        $bairros = [];
        if (!empty($hidrante['municipio_id'])) {
            $bairros = (new BairroRepository())->byMunicipio((int) $hidrante['municipio_id']);
        }

        $this->view('hidrantes/create', [
            'title' => 'Editar Hidrante',
            'formAction' => '/hidrantes/' . (int) $id . '/atualizar',
            'hidrante' => $hidrante,
            'municipios' => (new MunicipioRepository())->all(),
            'bairros' => $bairros,
        ]);
    }

    public function update(string $id): void
    {
        $auth = Session::get('auth');

        try {
            (new HidranteService())->update(
                (int) $id,
                $this->request->all(),
                $this->request->file('fotos') ?? [],
                $auth
            );

            $this->redirect('/hidrantes', 'Hidrante atualizado com sucesso.');
        } catch (ValidationException $e) {
            $this->redirect('/hidrantes/' . (int) $id . '/editar', null, $e->getMessage());
        } catch (\Throwable $e) {
            report_exception($e);
            $this->redirect('/hidrantes/' . (int) $id . '/editar', null, 'Nao foi possivel atualizar o hidrante agora.');
        }
    }

    public function destroy(string $id): void
    {
        $auth = Session::get('auth');

        try {
            (new HidranteService())->delete((int) $id, $auth);
            $this->redirect('/hidrantes', 'Hidrante excluido com sucesso.');
        } catch (\Throwable $e) {
            report_exception($e);
            $this->redirect('/hidrantes', null, 'Nao foi possivel excluir o hidrante agora.');
        }
    }

    public function mapData(): void
    {
        Response::json((new HidranteService())->mapPoints());
    }

    public function bairrosByMunicipio(string $municipioId): void
    {
        Response::json((new BairroRepository())->byMunicipio((int) $municipioId));
    }

    public function photo(string $filename): void
    {
        $safeFilename = basename($filename);
        $path = storage_path('uploads/hidrantes/' . $safeFilename);

        if ($safeFilename !== $filename || !is_file($path)) {
            http_response_code(404);
            echo 'Arquivo nao encontrado.';
            return;
        }

        $extension = strtolower(pathinfo($safeFilename, PATHINFO_EXTENSION));
        $contentType = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'webp' => 'image/webp',
            default => 'application/octet-stream',
        };

        header('Content-Type: ' . $contentType);
        header('Content-Length: ' . (string) filesize($path));
        header('Content-Disposition: inline; filename="' . rawurlencode($safeFilename) . '"');
        header('X-Content-Type-Options: nosniff');

        readfile($path);
        exit;
    }
}
