<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator\RuleDefinition;

readonly class RuleDefinition
{
    /**
     * @param int                  $id
     * @param int                  $tenantId
     * @param string               $name
     * @param array<string, mixed> $params
     */
    public function __construct(
        public int    $id,
        public int    $tenantId,
        public string $name,
        public array  $params,
    ) {}
}
