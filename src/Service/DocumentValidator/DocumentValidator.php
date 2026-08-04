<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator;

use App\Document;
use App\Service\DocumentValidator\RuleDefinition\RuleDefinitionRepositoryInterface;

readonly class DocumentValidator implements DocumentValidatorInterface
{
    /**
     * @param \App\Service\DocumentValidator\RuleDefinition\RuleDefinitionRepositoryInterface $ruleDefinitionRepository
     * @param \App\Service\DocumentValidator\RuleFactory                                      $ruleFactory
     */
    public function __construct(
        private RuleDefinitionRepositoryInterface $ruleDefinitionRepository,
        private RuleFactory                       $ruleFactory,
    ) {}

    /**
     * @param \App\Document $document
     *
     * @return \App\Service\DocumentValidator\ValidationResult
     * @throws \App\Service\DocumentValidator\Exception\RuleNotFoundException
     */
    public function validate(Document $document): ValidationResult
    {
        $ruleDefinitions = $this->ruleDefinitionRepository->forTenant($document->tenantId);

        $errors = [];
        foreach ($ruleDefinitions as $ruleDefinition) {
            $rule = $this->ruleFactory->make($ruleDefinition->name, $ruleDefinition->params);
            $errors = array_merge($errors, $rule->validate($document->content, $document->metadata));
        }

        return new ValidationResult($errors);
    }
}
