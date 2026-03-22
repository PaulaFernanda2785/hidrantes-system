<?php

namespace App\Services;

class LoginThrottleService
{
    private int $maxAttempts = 5;
    private int $windowSeconds = 900;
    private int $lockSeconds = 900;

    public function tooManyAttempts(string $matricula, string $ip): bool
    {
        $state = $this->loadState();
        $now = time();
        $state = $this->pruneState($state, $now);

        foreach ($this->keysFor($matricula, $ip) as $key) {
            $attempts = $state[$key]['attempts'] ?? [];
            $recentAttempts = array_values(array_filter(
                $attempts,
                fn(int $timestamp): bool => ($now - $timestamp) <= $this->windowSeconds
            ));

            if (count($recentAttempts) < $this->maxAttempts) {
                continue;
            }

            $lastAttemptAt = max($recentAttempts);
            if (($now - $lastAttemptAt) < $this->lockSeconds) {
                return true;
            }
        }

        return false;
    }

    public function remainingLockSeconds(string $matricula, string $ip): int
    {
        $state = $this->loadState();
        $now = time();
        $state = $this->pruneState($state, $now);
        $remaining = 0;

        foreach ($this->keysFor($matricula, $ip) as $key) {
            $attempts = $state[$key]['attempts'] ?? [];
            $recentAttempts = array_values(array_filter($attempts, fn(int $timestamp): bool => ($now - $timestamp) <= $this->windowSeconds));

            if (count($recentAttempts) < $this->maxAttempts) {
                continue;
            }

            $lastAttemptAt = max($recentAttempts);
            $remaining = max($remaining, $this->lockSeconds - ($now - $lastAttemptAt));
        }

        return max(0, $remaining);
    }

    public function hit(string $matricula, string $ip): void
    {
        $this->updateState(function (array $state) use ($matricula, $ip): array {
            $now = time();
            $state = $this->pruneState($state, $now);

            foreach ($this->keysFor($matricula, $ip) as $key) {
                $attempts = $state[$key]['attempts'] ?? [];
                $attempts[] = $now;
                $state[$key] = [
                    'attempts' => array_values(array_filter(
                        $attempts,
                        fn(int $timestamp): bool => ($now - $timestamp) <= $this->windowSeconds
                    )),
                ];
            }

            return $state;
        });
    }

    public function clear(string $matricula, string $ip): void
    {
        $this->updateState(function (array $state) use ($matricula, $ip): array {
            foreach ($this->keysFor($matricula, $ip) as $key) {
                unset($state[$key]);
            }

            return $state;
        });
    }

    private function keysFor(string $matricula, string $ip): array
    {
        $normalizedMatricula = mb_strtolower(trim($matricula));
        $normalizedIp = trim($ip) !== '' ? trim($ip) : '0.0.0.0';

        return [
            'login:ip:' . hash('sha256', $normalizedIp),
            'login:matricula_ip:' . hash('sha256', $normalizedMatricula . '|' . $normalizedIp),
        ];
    }

    private function filePath(): string
    {
        return storage_path('framework/security/login_throttle.json');
    }

    private function loadState(): array
    {
        $path = $this->filePath();

        if (!is_file($path)) {
            return [];
        }

        $content = file_get_contents($path);
        if ($content === false || trim($content) === '') {
            return [];
        }

        $decoded = json_decode($content, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function updateState(callable $callback): void
    {
        $path = $this->filePath();
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $handle = fopen($path, 'c+');
        if ($handle === false) {
            return;
        }

        try {
            if (!flock($handle, LOCK_EX)) {
                return;
            }

            $raw = stream_get_contents($handle);
            $state = json_decode($raw !== false ? $raw : '', true);
            if (!is_array($state)) {
                $state = [];
            }

            $updatedState = $callback($state);
            $encoded = json_encode($updatedState, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if ($encoded === false) {
                return;
            }

            ftruncate($handle, 0);
            rewind($handle);
            fwrite($handle, $encoded);
            fflush($handle);
        } finally {
            flock($handle, LOCK_UN);
            fclose($handle);
        }
    }

    private function pruneState(array $state, int $now): array
    {
        foreach ($state as $key => $entry) {
            $attempts = array_values(array_filter(
                $entry['attempts'] ?? [],
                fn(mixed $timestamp): bool => is_int($timestamp) && ($now - $timestamp) <= max($this->windowSeconds, $this->lockSeconds)
            ));

            if ($attempts === []) {
                unset($state[$key]);
                continue;
            }

            $state[$key] = ['attempts' => $attempts];
        }

        return $state;
    }
}
