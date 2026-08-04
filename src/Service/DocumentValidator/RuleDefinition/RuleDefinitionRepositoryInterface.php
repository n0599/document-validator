<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator\RuleDefinition;

interface RuleDefinitionRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return \App\Service\DocumentValidator\RuleDefinition\RuleDefinition[]
     */
    public function forTenant(int $id): array;
}
