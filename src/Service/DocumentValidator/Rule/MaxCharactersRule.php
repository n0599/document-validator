<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator\Rule;

use App\Service\DocumentValidator\ValidationError;

readonly class MaxCharactersRule implements RuleInterface
{
    /**
     * @param int $maxCharacters
     */
    public function __construct(
        private int $maxCharacters,
    ) {}

    /**
     * @param string                $content
     * @param array<string, string> $metadata
     *
     * @return \App\Service\DocumentValidator\ValidationError[]
     */
    public function validate(string $content, array $metadata): array
    {
        if (mb_strlen($content) <= $this->maxCharacters) {
            return [];
        }

        return [
            new ValidationError(
                message: "Content exceeds {$this->maxCharacters} characters",
                context: ['max' => $this->maxCharacters, 'actual' => mb_strlen($content)],
            ),
        ];
    }
}
