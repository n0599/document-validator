<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator;

readonly class ValidationError
{
    /**
     * @param string                    $message
     * @param array<string, string|int> $context
     */
    public function __construct(
        public string $message,
        public array  $context,
    ) {}
}
