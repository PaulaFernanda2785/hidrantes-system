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
            'status_operacional' => $this->request->input('status_operacional'),
            'municipio_id' => $this->request->input('municipio_id'),
        ];

        $service = new HidranteService();
        $municipios = (new MunicipioRepository())->all();

        $this->view('hidrantes/index', [
            'title' => 'Hidrantes',
            'hidrantes' => $service->list($filters),
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
            $this->redirect('/hidrantes/novo', null, $e->getMessage());
        }
    }

    public function edit(string $id): void
    {
        $service = new HidranteService();
        $hidrante = $service->find((int) $id);

        if (!$hidrante) {
            $this->redirect('/hidrantes', null, 'Hidrante não encontrado.');
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
            $this->redirect('/hidrantes/' . (int) $id . '/editar', null, $e->getMessage());
        }
    }

    public function destroy(string $id): void
    {
        $auth = Session::get('auth');

        try {
            (new HidranteService())->delete((int) $id, $auth);
            $this->redirect('/hidrantes', 'Hidrante excluído com sucesso.');
        } catch (\Throwable $e) {
            $this->redirect('/hidrantes', null, $e->getMessage());
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
}