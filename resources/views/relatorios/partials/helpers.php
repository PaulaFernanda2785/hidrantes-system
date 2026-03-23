<?php

if (!function_exists('relatorio_status_class')) {
    function relatorio_status_class(?string $status): string
    {
        return match (strtolower(trim((string) $status))) {
            'operante' => 'is-operante',
            'operante com restricao' => 'is-restricao',
            'inoperante' => 'is-inoperante',
            default => 'is-neutral',
        };
    }
}

if (!function_exists('relatorio_value')) {
    function relatorio_value(mixed $value, string $fallback = "N\u{00E3}o informado"): string
    {
        $normalized = preg_replace('/\s+/u', ' ', trim((string) $value));
        $normalized = is_string($normalized) ? trim($normalized) : '';

        return $normalized !== '' ? $normalized : $fallback;
    }
}

if (!function_exists('relatorio_status_label')) {
    function relatorio_status_label(?string $status, string $fallback = 'Todos'): string
    {
        $normalized = strtolower(trim((string) $status));

        if ($normalized === '') {
            return $fallback;
        }

        return match ($normalized) {
            'operante' => 'Operante',
            'operante com restricao' => "Operante com restri\u{00E7}\u{00E3}o",
            'inoperante' => 'Inoperante',
            default => relatorio_value($status, $fallback),
        };
    }
}

if (!function_exists('relatorio_lookup_nome')) {
    function relatorio_lookup_nome(array $items, string|int|null $id, string $fallback = 'Todos'): string
    {
        $target = trim((string) $id);

        if ($target === '') {
            return $fallback;
        }

        foreach ($items as $item) {
            if ((string) ($item['id'] ?? '') === $target) {
                return (string) ($item['nome'] ?? $fallback);
            }
        }

        return $fallback;
    }
}

if (!function_exists('relatorio_format_datetime')) {
    function relatorio_format_datetime(?string $value, string $fallback = "N\u{00E3}o informado"): string
    {
        $normalized = trim((string) $value);

        if ($normalized === '') {
            return $fallback;
        }

        $timestamp = strtotime($normalized);

        if ($timestamp === false) {
            return $normalized;
        }

        return date('d/m/Y H:i', $timestamp);
    }
}
