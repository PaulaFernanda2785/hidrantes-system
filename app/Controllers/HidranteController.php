<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Response;
use App\Core\Session;
use App\Repositories\BairroRepository;
use App\Repositories\MunicipioRepository;
use App\Services\BairroService;
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
            'bairro_id' => $this->request->input('bairro_id'),
        ];

        $page = (int) ($this->request->input('page') ?: 1);
        $perPage = 15;

        $service = new HidranteService();
        $result = $service->list($filters, $page, $perPage);

        $municipios = (new MunicipioRepository())->all();
        $bairros = [];

        if (!empty($filters['municipio_id'])) {
            $bairros = (new BairroRepository())->byMunicipio((int) $filters['municipio_id']);
        }

        $this->view('hidrantes/index', [
            'title' => 'Hidrantes',
            'hidrantes' => $result['data'],
            'pagination' => $result['meta'],
            'filters' => $filters,
            'municipios' => $municipios,
            'bairros' => $bairros,
            'scripts' => ['pages/hidrantes/index.js'],
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
            'scripts' => ['pages/hidrantes/form.js'],
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
            'scripts' => ['pages/hidrantes/form.js'],
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

    public function exportCsv(): void
    {
        $filters = [
            'q' => trim((string) $this->request->input('q')),
            'status_operacional' => $this->request->input('status_operacional'),
            'municipio_id' => $this->request->input('municipio_id'),
            'bairro_id' => $this->request->input('bairro_id'),
        ];

        $items = (new HidranteService())->export($filters);
        $filename = 'hidrantes_' . date('Y-m-d_H-i-s') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');

        $output = fopen('php://output', 'wb');
        if ($output === false) {
            http_response_code(500);
            echo 'Nao foi possivel gerar o CSV.';
            exit;
        }

        fwrite($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'ID',
            'Numero do Hidrante',
            'Equipe Responsavel',
            'Area',
            'Existe no Local',
            'Tipo de Hidrante',
            'Acessibilidade',
            'Tampo e Conexoes',
            'Tampas Ausentes',
            'Caixa de Protecao',
            'Condicao da Caixa',
            'Presenca de Agua no Interior',
            'Teste Realizado',
            'Resultado do Teste',
            'Status Operacional',
            'Municipio ID',
            'Municipio',
            'Bairro ID',
            'Bairro',
            'Endereco',
            'Latitude',
            'Longitude',
            'Foto 01',
            'Foto 02',
            'Foto 03',
            'Criado em',
            'Atualizado em',
            'Criado por Usuario ID',
            'Atualizado por Usuario ID',
        ], ';');

        foreach ($items as $item) {
            fputcsv($output, [
                $item['id'] ?? '',
                $item['numero_hidrante'] ?? '',
                $item['equipe_responsavel'] ?? '',
                $item['area'] ?? '',
                $item['existe_no_local'] ?? '',
                $item['tipo_hidrante'] ?? '',
                $item['acessibilidade'] ?? '',
                $item['tampo_conexoes'] ?? '',
                $item['tampas_ausentes'] ?? '',
                $item['caixa_protecao'] ?? '',
                $item['condicao_caixa'] ?? '',
                $item['presenca_agua_interior'] ?? '',
                $item['teste_realizado'] ?? '',
                $item['resultado_teste'] ?? '',
                $item['status_operacional'] ?? '',
                $item['municipio_id'] ?? '',
                $item['municipio_nome'] ?? '',
                $item['bairro_id'] ?? '',
                $item['bairro_nome'] ?? '',
                $item['endereco'] ?? '',
                $item['latitude'] ?? '',
                $item['longitude'] ?? '',
                $this->photoUrl((string) ($item['foto_01'] ?? '')),
                $this->photoUrl((string) ($item['foto_02'] ?? '')),
                $this->photoUrl((string) ($item['foto_03'] ?? '')),
                $item['criado_em'] ?? '',
                $item['atualizado_em'] ?? '',
                $item['criado_por_usuario_id'] ?? '',
                $item['atualizado_por_usuario_id'] ?? '',
            ], ';');
        }

        fclose($output);
        exit;
    }

    public function bairrosByMunicipio(string $municipioId): void
    {
        Response::json((new BairroRepository())->byMunicipio((int) $municipioId));
    }

    public function storeBairro(): void
    {
        $auth = Session::get('auth', []);

        try {
            $bairro = (new BairroService())->create($this->request->all(), $auth);
            Response::json($bairro);
        } catch (ValidationException $e) {
            Response::json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            report_exception($e);
            Response::json([
                'message' => 'Nao foi possivel cadastrar o bairro agora.',
            ], 500);
        }
    }

    public function updateBairro(string $id): void
    {
        $auth = Session::get('auth', []);

        try {
            $bairro = (new BairroService())->update((int) $id, $this->request->all(), $auth);
            Response::json($bairro);
        } catch (ValidationException $e) {
            Response::json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            report_exception($e);
            Response::json([
                'message' => 'Nao foi possivel atualizar o bairro agora.',
            ], 500);
        }
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

    private function photoUrl(string $filename): string
    {
        $safeFilename = trim($filename);
        if ($safeFilename === '') {
            return '';
        }

        $scheme = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $path = '/uploads/hidrantes/' . rawurlencode($safeFilename);

        return $host !== '' ? $scheme . '://' . $host . $path : $path;
    }
}
