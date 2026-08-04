<?php

declare(strict_types=1);

namespace Tests\Service\DocumentValidator;

use App\Service\DocumentValidator\Exception\DuplicateRuleException;
use App\Service\DocumentValidator\Exception\RuleNotFoundException;
use App\Service\DocumentValidator\Rule\RuleInterface;
use App\Service\DocumentValidator\RuleFactory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RuleFactoryTest extends TestCase
{
    #[Test]
    public function it_creates_rule_by_registered_name(): void
    {
        $factory = new RuleFactory();
        $rule = $this->createStub(RuleInterface::class);

        $factory->register('test_rule', fn(array $params) => $rule);

        $this->assertSame($rule, $factory->make('test_rule', []));
    }

    #[Test]
    public function it_passes_params_to_callback(): void
    {
        $factory = new RuleFactory();
        $receivedParams = null;
        $rule = $this->createStub(RuleInterface::class);

        $factory->register('test_rule', function (array $params) use (&$receivedParams, $rule) {
            $receivedParams = $params;
            return $rule;
        });

        $factory->make('test_rule', ['key' => 'value']);

        $this->assertSame(['key' => 'value'], $receivedParams);
    }

    #[Test]
    public function it_throws_on_duplicate_registration(): void
    {
        $factory = new RuleFactory();
        $factory->register('test_rule', fn(array $params) => $this->createMock(RuleInterface::class));

        $this->expectException(DuplicateRuleException::class);

        $factory->register('test_rule', fn(array $params) => $this->createMock(RuleInterface::class));
    }

    #[Test]
    public function it_throws_on_unregistered_rule(): void
    {
        $factory = new RuleFactory();

        $this->expectException(RuleNotFoundException::class);

        $factory->make('nonexistent', []);
    }
}
