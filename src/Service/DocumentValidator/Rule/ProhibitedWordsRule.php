<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator\Rule;

use App\Service\DocumentValidator\ValidationError;

readonly class ProhibitedWordsRule implements RuleInterface
{
    /**
     * @param string[] $words
     */
    public function __construct(
        private array $words,
    ) {}

    /**
     * @param string                $content
     * @param array<string, string> $metadata
     *
     * @return \App\Service\DocumentValidator\ValidationError[]
     */
    public function validate(string $content, array $metadata): array
    {
        $errors = [];

        foreach ($this->words as $word) {
            if (mb_stripos($content, $word) !== false) {
                $errors[] = new ValidationError(
                    message: "Content contains prohibited word: {$word}",
                    context: ['word' => $word],
                );
            }
        }

        return $errors;
    }
}
