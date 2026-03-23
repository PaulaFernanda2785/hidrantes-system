<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Repositories\BairroRepository;
use App\Repositories\MunicipioRepository;
use App\Services\RelatorioService;

class RelatorioController extends Controller
{
    public function index(): void
    {
        $filters = $this->filters();

        $this->view('relatorios/index', array_merge($this->viewData($filters), [
            'title' => "Relat\u{00F3}rio T\u{00E9}cnico de Hidrantes",
            'pageStylesheets' => ['pages/relatorios.css'],
            'scripts' => ['pages/relatorios/index.js'],
            'printPreviewUrl' => $this->buildPrintPreviewUrl($filters),
        ]));
    }

    public function print(): void
    {
        $filters = $this->filters();

        $this->view('relatorios/print', array_merge($this->viewData($filters), [
            'title' => "Relat\u{00F3}rio T\u{00E9}cnico de Hidrantes",
            'pageStylesheets' => ['pages/relatorios-document.css'],
        ]), 'layouts/document');
    }

    private function filters(): array
    {
        return [
            'q' => trim((string) $this->request->input('q')),
            'status_operacional' => $this->request->input('status_operacional'),
            'municipio_id' => $this->request->input('municipio_id'),
            'bairro_id' => $this->request->input('bairro_id'),
        ];
    }

    private function viewData(array $filters): array
    {
        $municipios = (new MunicipioRepository())->all();
        $bairros = [];

        if (trim((string) ($filters['municipio_id'] ?? '')) !== '') {
            $bairros = (new BairroRepository())->byMunicipio((int) $filters['municipio_id']);
        }

        $service = new RelatorioService();
        $items = $service->hidrantes($filters);
        $auth = Session::get('auth', []);

        return [
            'items' => $items,
            'municipios' => $municipios,
            'bairros' => $bairros,
            'filters' => $filters,
            'document' => $service->buildDocument(
                $items,
                $filters,
                $municipios,
                $bairros,
                (string) ($auth['nome'] ?? 'Sistema'),
            ),
        ];
    }

    private function buildPrintPreviewUrl(array $filters): string
    {
        $query = http_build_query(array_filter(
            $filters,
            static fn (mixed $value): bool => trim((string) $value) !== ''
        ));

        if ($query === '') {
            return '/relatorios/hidrantes/impressao';
        }

        return '/relatorios/hidrantes/impressao?' . $query;
    }
}
