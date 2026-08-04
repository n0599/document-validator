<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator\Rule;

use App\Service\DocumentValidator\ValidationError;

readonly class RequiredMetadataFieldsRule implements RuleInterface
{
    /**
     * @param string[] $fields
     */
    public function __construct(
        private array $fields,
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

        foreach ($this->fields as $field) {
            if (!array_key_exists($field, $metadata)) {
                $errors[] = new ValidationError(
                    message: "Required metadata field '{$field}' is missing",
                    context: ['field' => $field],
                );
            }
        }

        return $errors;
    }
}
