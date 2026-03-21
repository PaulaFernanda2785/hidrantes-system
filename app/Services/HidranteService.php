<?php

namespace App\Services;

use App\Repositories\BairroRepository;
use App\Repositories\HidranteRepository;
use App\Validators\ValidationException;

class HidranteService
{
    public function __construct(
        private ?HidranteRepository $hidranteRepository = null,
        private ?BairroRepository $bairroRepository = null,
        private ?UploadService $uploadService = null,
        private ?GeoService $geoService = null,
        private ?AuditService $auditService = null,
    ) {
        $this->hidranteRepository ??= new HidranteRepository();
        $this->bairroRepository ??= new BairroRepository();
        $this->uploadService ??= new UploadService();
        $this->geoService ??= new GeoService();
        $this->auditService ??= new AuditService();
    }

    public function list(array $filters = [], int $page = 1, int $perPage = 15): array
    {
        return $this->hidranteRepository->paginate($filters, $page, $perPage);
    }

    public function find(int $id): ?array
    {
        return $this->hidranteRepository->findById($id);
    }

    public function create(array $data, array $files, array $actor): int
    {
        $payload = $this->validateAndNormalize($data);

        if ($this->hidranteRepository->existsByNumero($payload['numero_hidrante'])) {
            throw new ValidationException([
                'numero_hidrante' => 'Ja existe um hidrante com esse numero.',
            ]);
        }

        $images = $this->uploadService->storeMultiple($files);

        $payload['foto_01'] = $images['foto_01'];
        $payload['foto_02'] = $images['foto_02'];
        $payload['foto_03'] = $images['foto_03'];
        $payload['criado_por_usuario_id'] = $actor['id'];
        $payload['atualizado_por_usuario_id'] = $actor['id'];

        $id = $this->hidranteRepository->create($payload);

        $this->recordAuditSafely(
            $actor,
            'cadastrar',
            'hidrantes',
            (string) $id,
            'Cadastro de hidrante realizado.'
        );

        return $id;
    }

    public function update(int $id, array $data, array $files, array $actor): void
    {
        $current = $this->hidranteRepository->findById($id);

        if (!$current) {
            throw new \RuntimeException('Hidrante nao encontrado.');
        }

        $payload = $this->validateAndNormalize($data);

        if (($actor['perfil'] ?? '') === 'operador') {
            if ($payload['numero_hidrante'] !== (string) $current['numero_hidrante']) {
                throw new ValidationException([
                    'numero_hidrante' => 'Operador nao pode alterar o numero do hidrante.',
                ]);
            }

            $payload['numero_hidrante'] = (string) $current['numero_hidrante'];
        }

        if ($this->hidranteRepository->existsByNumero($payload['numero_hidrante'], $id)) {
            throw new ValidationException([
                'numero_hidrante' => 'Ja existe outro hidrante com esse numero.',
            ]);
        }

        $images = $this->uploadService->storeMultiple($files);

        $payload['foto_01'] = $images['foto_01'] ?: $current['foto_01'];
        $payload['foto_02'] = $images['foto_02'] ?: $current['foto_02'];
        $payload['foto_03'] = $images['foto_03'] ?: $current['foto_03'];
        $payload['atualizado_por_usuario_id'] = $actor['id'];

        $this->hidranteRepository->update($id, $payload);

        $this->recordAuditSafely(
            $actor,
            'editar',
            'hidrantes',
            (string) $id,
            'Edicao de hidrante realizada.'
        );
    }

    public function delete(int $id, array $actor): void
    {
        $current = $this->hidranteRepository->findById($id);

        if (!$current) {
            throw new \RuntimeException('Hidrante nao encontrado.');
        }

        $this->hidranteRepository->softDelete($id, (int) $actor['id']);

        $this->recordAuditSafely(
            $actor,
            'deletar',
            'hidrantes',
            (string) $id,
            'Exclusao logica de hidrante realizada.'
        );
    }

    public function mapPoints(): array
    {
        return $this->hidranteRepository->mapPoints();
    }

    private function validateAndNormalize(array $data): array
    {
        $errors = [];

        $required = [
            'numero_hidrante',
            'equipe_responsavel',
            'area',
            'existe_no_local',
            'tipo_hidrante',
            'acessibilidade',
            'tampo_conexoes',
            'caixa_protecao',
            'presenca_agua_interior',
            'teste_realizado',
            'status_operacional',
            'municipio_id',
            'endereco',
        ];

        foreach ($required as $field) {
            if (trim((string) ($data[$field] ?? '')) === '') {
                $errors[$field] = 'Campo obrigatorio.';
            }
        }

        $areas = ['urbano', 'industrial', 'rural'];
        $simNao = ['sim', 'nao'];
        $tipos = ['coluna', 'subterraneo', 'parede', 'outro'];
        $tampoConexoes = ['integra', 'danificadas', 'ausentes'];
        $condicoesCaixa = ['boa', 'regular', 'ruim'];
        $resultadosTeste = ['funcionando normalmente', 'vazamento', 'vazao insuficiente', 'nao funcionou'];
        $statusOperacional = ['operante', 'operante com restricao', 'inoperante'];

        $numero = trim((string) ($data['numero_hidrante'] ?? ''));
        $equipe = trim((string) ($data['equipe_responsavel'] ?? ''));
        $area = trim((string) ($data['area'] ?? ''));
        $existeNoLocal = trim((string) ($data['existe_no_local'] ?? ''));
        $tipo = trim((string) ($data['tipo_hidrante'] ?? ''));
        $acessibilidade = trim((string) ($data['acessibilidade'] ?? ''));
        $tampo = trim((string) ($data['tampo_conexoes'] ?? ''));
        $tampasAusentes = trim((string) ($data['tampas_ausentes'] ?? ''));
        $caixaProtecao = trim((string) ($data['caixa_protecao'] ?? ''));
        $condicaoCaixa = trim((string) ($data['condicao_caixa'] ?? ''));
        $presencaAgua = trim((string) ($data['presenca_agua_interior'] ?? ''));
        $testeRealizado = trim((string) ($data['teste_realizado'] ?? ''));
        $resultadoTeste = trim((string) ($data['resultado_teste'] ?? ''));
        $status = trim((string) ($data['status_operacional'] ?? ''));
        $municipioId = (int) ($data['municipio_id'] ?? 0);
        $bairroId = !empty($data['bairro_id']) ? (int) $data['bairro_id'] : null;
        $endereco = trim((string) ($data['endereco'] ?? ''));
        $latitudeRaw = trim((string) ($data['latitude'] ?? ''));
        $longitudeRaw = trim((string) ($data['longitude'] ?? ''));

        if ($numero !== '' && mb_strlen($numero) > 20) {
            $errors['numero_hidrante'] = 'Numero do hidrante excede 20 caracteres.';
        }

        if ($equipe !== '' && mb_strlen($equipe) > 150) {
            $errors['equipe_responsavel'] = 'Equipe responsavel excede 150 caracteres.';
        }

        if ($endereco !== '' && mb_strlen($endereco) > 255) {
            $errors['endereco'] = 'Endereco excede 255 caracteres.';
        }

        if ($area && !in_array($area, $areas, true)) {
            $errors['area'] = 'Area invalida.';
        }

        if ($existeNoLocal && !in_array($existeNoLocal, $simNao, true)) {
            $errors['existe_no_local'] = 'Valor invalido.';
        }

        if ($tipo && !in_array($tipo, $tipos, true)) {
            $errors['tipo_hidrante'] = 'Tipo de hidrante invalido.';
        }

        if ($acessibilidade && !in_array($acessibilidade, $simNao, true)) {
            $errors['acessibilidade'] = 'Valor invalido.';
        }

        if ($tampo && !in_array($tampo, $tampoConexoes, true)) {
            $errors['tampo_conexoes'] = 'Valor invalido.';
        }

        if ($caixaProtecao && !in_array($caixaProtecao, $simNao, true)) {
            $errors['caixa_protecao'] = 'Valor invalido.';
        }

        if ($condicaoCaixa !== '' && !in_array($condicaoCaixa, $condicoesCaixa, true)) {
            $errors['condicao_caixa'] = 'Condicao da caixa invalida.';
        }

        if ($presencaAgua && !in_array($presencaAgua, $simNao, true)) {
            $errors['presenca_agua_interior'] = 'Valor invalido.';
        }

        if ($testeRealizado && !in_array($testeRealizado, $simNao, true)) {
            $errors['teste_realizado'] = 'Valor invalido.';
        }

        if ($resultadoTeste !== '' && !in_array($resultadoTeste, $resultadosTeste, true)) {
            $errors['resultado_teste'] = 'Resultado do teste invalido.';
        }

        if ($status && !in_array($status, $statusOperacional, true)) {
            $errors['status_operacional'] = 'Status operacional invalido.';
        }

        if ($municipioId <= 0) {
            $errors['municipio_id'] = 'Municipio invalido.';
        }

        if ($bairroId !== null) {
            if ($bairroId <= 0) {
                $errors['bairro_id'] = 'Bairro invalido.';
            } elseif ($municipioId > 0 && !$this->bairroRepository->belongsToMunicipio($bairroId, $municipioId)) {
                $errors['bairro_id'] = 'O bairro selecionado nao pertence ao municipio informado.';
            }
        }

        if ($testeRealizado === 'nao') {
            $resultadoTeste = '';
        }

        if ($caixaProtecao === 'nao') {
            $condicaoCaixa = '';
        }

        $latitude = null;
        $longitude = null;

        if ($latitudeRaw !== '' || $longitudeRaw !== '') {
            if (!$this->geoService->isValid($latitudeRaw, $longitudeRaw)) {
                $errors['coordenadas'] = 'Latitude/longitude invalidas.';
            } else {
                $latitude = $latitudeRaw;
                $longitude = $longitudeRaw;
            }
        }

        if ($errors) {
            throw new ValidationException($errors, 'Verifique os campos informados.');
        }

        return [
            'numero_hidrante' => $numero,
            'equipe_responsavel' => $equipe,
            'area' => $area,
            'existe_no_local' => $existeNoLocal,
            'tipo_hidrante' => $tipo,
            'acessibilidade' => $acessibilidade,
            'tampo_conexoes' => $tampo,
            'tampas_ausentes' => $tampasAusentes !== '' ? $tampasAusentes : null,
            'caixa_protecao' => $caixaProtecao,
            'condicao_caixa' => $condicaoCaixa !== '' ? $condicaoCaixa : null,
            'presenca_agua_interior' => $presencaAgua,
            'teste_realizado' => $testeRealizado,
            'resultado_teste' => $resultadoTeste !== '' ? $resultadoTeste : null,
            'status_operacional' => $status,
            'municipio_id' => $municipioId,
            'bairro_id' => $bairroId,
            'endereco' => $endereco,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];
    }

    private function recordAuditSafely(array $actor, string $acao, string $entidade, string $referencia, string $detalhes): void
    {
        try {
            $this->auditService->record($actor, $acao, $entidade, $referencia, $detalhes);
        } catch (\Throwable $e) {
            report_exception($e);
        }
    }
}
