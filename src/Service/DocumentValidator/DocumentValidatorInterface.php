<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator;

use App\Document;

interface DocumentValidatorInterface
{
    /**
     * @param \App\Document $document
     *
     * @return \App\Service\DocumentValidator\ValidationResult
     * @throws \App\Service\DocumentValidator\Exception\RuleNotFoundException
     */
    public function validate(Document $document): ValidationResult;
}
