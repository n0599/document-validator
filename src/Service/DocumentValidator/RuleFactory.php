<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator;

use App\Service\DocumentValidator\Exception\DuplicateRuleException;
use App\Service\DocumentValidator\Exception\RuleNotFoundException;
use App\Service\DocumentValidator\Rule\RuleInterface;

class RuleFactory
{
    /**
     * @var array<string, callable(array<string, mixed> $params): RuleInterface>
     */
    private array $factoryByName = [];

    /**
     * @param string                                                $name
     * @param callable(array<string, mixed> $params): RuleInterface $callback
     * @throws \App\Service\DocumentValidator\Exception\DuplicateRuleException
     */
    public function register(string $name, callable $callback): void
    {
        if (array_key_exists($name, $this->factoryByName)) {
            throw new DuplicateRuleException("Validation rule '{$name}' is already registered");
        }
        $this->factoryByName[$name] = $callback;
    }

    /**
     * @param string               $name
     * @param array<string, mixed> $params
     *
     * @return \App\Service\DocumentValidator\Rule\RuleInterface
     * @throws \App\Service\DocumentValidator\Exception\RuleNotFoundException
     */
    public function make(string $name, array $params): RuleInterface
    {
        if (!array_key_exists($name, $this->factoryByName)) {
            throw new RuleNotFoundException("Rule '{$name}' was not found. Did you forget to register it?");
        }

        $factory = $this->factoryByName[$name];
        return $factory($params);
    }
}
