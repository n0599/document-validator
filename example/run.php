<?php

declare(strict_types=1);

namespace Example;

require __DIR__ . '/../vendor/autoload.php';

use App\Document;
use App\Service\DocumentValidator\DocumentValidator;
use App\Service\DocumentValidator\Exception\DocumentValidatorExceptionInterface;
use App\Service\DocumentValidator\Rule\MaxCharactersRule;
use App\Service\DocumentValidator\Rule\ProhibitedWordsRule;
use App\Service\DocumentValidator\Rule\RequiredMetadataFieldsRule;
use App\Service\DocumentValidator\RuleDefinition\InMemoryRuleDefinitionRepository;
use App\Service\DocumentValidator\RuleDefinition\RuleDefinition;
use App\Service\DocumentValidator\RuleFactory;
use App\Service\DocumentValidator\ValidationResult;

// --- Rule factory setup (could be a framework service provider) ---

$ruleFactory = new RuleFactory();
$ruleFactory->register('max_characters', function (array $params) {
    /** @var array{max: int<0, max>} $params */
    return new MaxCharactersRule((int) $params['max']);
});
$ruleFactory->register('prohibited_words', function (array $params) {
    /** @var array{words: string[]} $params */
    return new ProhibitedWordsRule($params['words']);
});
$ruleFactory->register('required_metadata_fields', function (array $params) {
    /** @var array{field_names: string[]} $params */
    return new RequiredMetadataFieldsRule($params['field_names']);
});

// --- Tenant rule definitions (in real app these come from a database) ---

$ruleDefinitionRepository = new InMemoryRuleDefinitionRepository();

// Tenant #1 — a strict content policy
$ruleDefinitionRepository->save(new RuleDefinition(1, 1, 'max_characters', ['max' => 50]));
$ruleDefinitionRepository->save(new RuleDefinition(2, 1, 'prohibited_words', ['words' => ['spam', 'viagra']]));
$ruleDefinitionRepository->save(new RuleDefinition(3, 1, 'required_metadata_fields', ['field_names' => ['author', 'title']]));

// Tenant #2 — a soft policy, only metadata required
$ruleDefinitionRepository->save(new RuleDefinition(4, 2, 'max_characters', ['max' => 500]));
$ruleDefinitionRepository->save(new RuleDefinition(5, 2, 'required_metadata_fields', ['field_names' => ['category']]));

// --- Validator ---

$documentValidator = new DocumentValidator($ruleDefinitionRepository, $ruleFactory);

// --- Documents ---

$documents = [
    new Document(1, 1, 'Clean and short', ['author' => 'John', 'title' => 'Report']),
    new Document(2, 1, 'This document contains spam and is way too long to fit within the fifty character limit', []),
    new Document(3, 2, 'A longer document is perfectly fine for tenant two.', ['category' => 'article']),
    new Document(4, 2, 'Missing required metadata for this tenant.', []),
];

// --- Validation ---

foreach ($documents as $document) {
    echo "Document #{$document->id} (tenant #{$document->tenantId})\n";

    try {
        $result = $documentValidator->validate($document);
    } catch (DocumentValidatorExceptionInterface $e) {
        echo "  Error: {$e->getMessage()}\n\n";
        continue;
    }

    printResult($result);
    echo "\n";
}

function printResult(ValidationResult $result): void
{
    if ($result->isValid()) {
        echo "  ✓ Valid\n";
        return;
    }

    foreach ($result->getErrors() as $error) {
        echo "  ✗ {$error->message}\n";
    }
}
