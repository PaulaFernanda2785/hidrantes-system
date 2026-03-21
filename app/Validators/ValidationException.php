<?php

namespace App\Validators;

class ValidationException extends \RuntimeException
{
    public function __construct(
        private array $errors = [],
        string $message = ''
    ) {
        if ($message === '') {
            $firstError = reset($this->errors);
            $message = is_string($firstError) && $firstError !== ''
                ? $firstError
                : 'Verifique os dados informados.';
        }

        parent::__construct($message);
    }

    public function errors(): array
    {
        return $this->errors;
    }
}
