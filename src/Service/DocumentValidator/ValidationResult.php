<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator;

readonly class ValidationResult
{
    /**
     * @param \App\Service\DocumentValidator\ValidationError[] $errors
     */
    public function __construct(
        private array $errors,
    ) {}

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return count($this->errors) === 0;
    }

    /**
     * @return \App\Service\DocumentValidator\ValidationError[]
     */
    public function getErrors(): array
    {
        return $this->errors;
    }
}
