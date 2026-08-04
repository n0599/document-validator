<?php

declare(strict_types=1);

namespace Tests\Service\DocumentValidator\RuleDefinition;

use App\Service\DocumentValidator\RuleDefinition\InMemoryRuleDefinitionRepository;
use App\Service\DocumentValidator\RuleDefinition\RuleDefinition;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InMemoryRuleDefinitionRepositoryTest extends TestCase
{
    #[Test]
    public function it_returns_definitions_for_tenant(): void
    {
        $repository = new InMemoryRuleDefinitionRepository();
        $definition = new RuleDefinition(1, 10, 'max_characters', ['maxCharacters' => 100]);

        $repository->save($definition);

        $result = $repository->forTenant(10);
        $this->assertCount(1, $result);
        $this->assertSame($definition, reset($result));
    }

    #[Test]
    public function it_does_not_return_definitions_of_other_tenant(): void
    {
        $repository = new InMemoryRuleDefinitionRepository();
        $repository->save(new RuleDefinition(1, 10, 'max_characters', []));

        $result = $repository->forTenant(99);

        $this->assertEmpty($result);
    }

    #[Test]
    public function it_returns_empty_array_when_no_definitions(): void
    {
        $repository = new InMemoryRuleDefinitionRepository();

        $this->assertEmpty($repository->forTenant(10));
    }

    #[Test]
    public function it_returns_multiple_definitions_for_same_tenant(): void
    {
        $repository = new InMemoryRuleDefinitionRepository();
        $repository->save(new RuleDefinition(1, 10, 'max_characters', []));
        $repository->save(new RuleDefinition(2, 10, 'prohibited_words', []));

        $result = $repository->forTenant(10);

        $this->assertCount(2, $result);
    }
}
