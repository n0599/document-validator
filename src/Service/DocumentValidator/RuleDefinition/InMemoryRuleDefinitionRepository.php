<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator\RuleDefinition;

class InMemoryRuleDefinitionRepository implements RuleDefinitionRepositoryInterface
{
    /**
     * @var \App\Service\DocumentValidator\RuleDefinition\RuleDefinition[]
     */
    private array $storage = [];

    /**
     * @param int $id
     *
     * @return \App\Service\DocumentValidator\RuleDefinition\RuleDefinition[]
     */
    public function forTenant(int $id): array
    {
        return array_filter($this->storage, function ($ruleDefinition) use ($id) {
            return $ruleDefinition->tenantId === $id;
        });
    }

    /**
     * @param \App\Service\DocumentValidator\RuleDefinition\RuleDefinition $ruleDefinition
     */
    public function save(RuleDefinition $ruleDefinition): void
    {
        $this->storage[] = $ruleDefinition;
    }
}
