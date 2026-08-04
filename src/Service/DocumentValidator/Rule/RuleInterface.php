<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator\Rule;

interface RuleInterface
{
    /**
     * @param string                $content
     * @param array<string, string> $metadata
     *
     * @return \App\Service\DocumentValidator\ValidationError[]
     */
    public function validate(string $content, array $metadata): array;
}
