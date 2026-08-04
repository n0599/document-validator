<?php

declare(strict_types=1);

namespace Tests\Service\DocumentValidator;

use App\Document;
use App\Service\DocumentValidator\DocumentValidator;
use App\Service\DocumentValidator\Exception\RuleNotFoundException;
use App\Service\DocumentValidator\Rule\MaxCharactersRule;
use App\Service\DocumentValidator\Rule\ProhibitedWordsRule;
use App\Service\DocumentValidator\RuleDefinition\InMemoryRuleDefinitionRepository;
use App\Service\DocumentValidator\RuleDefinition\RuleDefinition;
use App\Service\DocumentValidator\RuleFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DocumentValidatorTest extends TestCase
{
    #[Test]
    public function it_returns_valid_result_when_tenant_has_no_rules(): void
    {
        $validator = $this->createValidator();

        $result = $validator->validate(new Document(1, 10, 'content', []));

        $this->assertTrue($result->isValid());
    }

    #[Test]
    public function it_returns_valid_result_when_document_passes_all_rules(): void
    {
        $repository = new InMemoryRuleDefinitionRepository();
        $repository->save(new RuleDefinition(1, 10, 'max_characters', ['maxCharacters' => 100]));

        $validator = $this->createValidator($repository);

        $result = $validator->validate(new Document(1, 10, 'short', []));

        $this->assertTrue($result->isValid());
    }

    #[Test]
    public function it_returns_errors_when_document_fails_rule(): void
    {
        $repository = new InMemoryRuleDefinitionRepository();
        $repository->save(new RuleDefinition(1, 10, 'max_characters', ['maxCharacters' => 3]));

        $validator = $this->createValidator($repository);

        $result = $validator->validate(new Document(1, 10, 'too long', []));

        $this->assertFalse($result->isValid());
        $this->assertCount(1, $result->getErrors());
    }

    #[Test]
    public function it_aggregates_errors_from_multiple_rules(): void
    {
        $repository = new InMemoryRuleDefinitionRepository();
        $repository->save(new RuleDefinition(1, 10, 'max_characters', ['maxCharacters' => 3]));
        $repository->save(new RuleDefinition(2, 10, 'prohibited_words', ['words' => ['long']]));

        $validator = $this->createValidator($repository);

        $result = $validator->validate(new Document(1, 10, 'too long', []));

        $this->assertFalse($result->isValid());
        $this->assertCount(2, $result->getErrors());
    }

    #[Test]
    public function it_throws_when_rule_is_not_registered(): void
    {
        $repository = new InMemoryRuleDefinitionRepository();
        $repository->save(new RuleDefinition(1, 10, 'unknown_rule', []));

        $factory = new RuleFactory();
        $validator = new DocumentValidator($repository, $factory);

        $this->expectException(RuleNotFoundException::class);

        $validator->validate(new Document(1, 10, 'content', []));
    }

    private function createValidator(?InMemoryRuleDefinitionRepository $repository = null): DocumentValidator
    {
        $repository ??= new InMemoryRuleDefinitionRepository();

        $factory = new RuleFactory();
        $factory->register('max_characters', fn(array $params) => new MaxCharactersRule($params['maxCharacters']));
        $factory->register('prohibited_words', fn(array $params) => new ProhibitedWordsRule($params['words']));

        return new DocumentValidator($repository, $factory);
    }
}
