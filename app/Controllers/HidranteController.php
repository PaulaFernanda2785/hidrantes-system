<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\MunicipioRepository;
use App\Repositories\BairroRepository;
use App\Services\HidranteService;

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
            'municipios' => (new MunicipioRepository())->all(),
            'bairros' => [],
        ]);
    }

    public function store(): void
    {
        $auth = Session::get('auth');
        $required = ['numero_hidrante', 'equipe_responsavel', 'area', 'tipo_hidrante', 'status_operacional', 'municipio_id', 'endereco'];
        foreach ($required as $field) {
            if (trim((string) $this->request->input($field)) === '') {
                $this->redirect('/hidrantes/novo', null, 'Preencha todos os campos obrigatórios do hidrante.');
            }
        }

        try {
            (new HidranteService())->create($this->request->all(), $this->request->file('fotos') ?? [], $auth);
            $this->redirect('/hidrantes', 'Hidrante cadastrado com sucesso.');
        } catch (\Throwable $e) {
            $this->redirect('/hidrantes/novo', null, $e->getMessage());
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
