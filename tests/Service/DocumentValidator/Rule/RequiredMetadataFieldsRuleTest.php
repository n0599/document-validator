<?php

declare(strict_types=1);

namespace Tests\Service\DocumentValidator\Rule;

use App\Service\DocumentValidator\Rule\RequiredMetadataFieldsRule;
use App\Service\DocumentValidator\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RequiredMetadataFieldsRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('passingMetadataProvider')]
    public function it_passes_validation(array $fields, array $metadata): void
    {
        $rule = new RequiredMetadataFieldsRule($fields);

        $errors = $rule->validate('', $metadata);

        $this->assertEmpty($errors);
    }

    #[Test]
    #[DataProvider('failingMetadataProvider')]
    public function it_fails_validation(array $fields, array $metadata, int $expectedErrorCount): void
    {
        $rule = new RequiredMetadataFieldsRule($fields);

        $errors = $rule->validate('', $metadata);

        $this->assertCount($expectedErrorCount, $errors);
    }

    #[Test]
    public function it_returns_validation_error_instance(): void
    {
        $rule = new RequiredMetadataFieldsRule(['author']);

        $errors = $rule->validate('', []);

        $this->assertInstanceOf(ValidationError::class, $errors[0]);
    }

    #[Test]
    public function it_returns_error_message_with_field_name(): void
    {
        $rule = new RequiredMetadataFieldsRule(['author']);

        $errors = $rule->validate('', []);

        $this->assertSame("Required metadata field 'author' is missing", $errors[0]->message);
    }

    #[Test]
    public function it_returns_error_context_with_field(): void
    {
        $rule = new RequiredMetadataFieldsRule(['author']);

        $errors = $rule->validate('', []);

        $this->assertSame('author', $errors[0]->context['field']);
    }

    #[Test]
    public function it_returns_errors_only_for_missing_fields(): void
    {
        $rule = new RequiredMetadataFieldsRule(['author', 'title', 'date']);

        $errors = $rule->validate('', ['title' => 'Test']);

        $this->assertCount(2, $errors);
        $this->assertSame('author', $errors[0]->context['field']);
        $this->assertSame('date', $errors[1]->context['field']);
    }

    /**
     * @return iterable<string, array{string[], array<string, string>}>
     */
    public static function passingMetadataProvider(): iterable
    {
        yield 'all required fields present' => [['author', 'title'], ['author' => 'John', 'title' => 'Doc']];
        yield 'empty required fields list' => [[], []];
        yield 'empty fields with empty metadata' => [[], []];
        yield 'extra fields in metadata' => [['author'], ['author' => 'John', 'title' => 'Doc', 'date' => '2024-01-01']];
        yield 'field present with empty value' => [['author'], ['author' => '']];
    }

    /**
     * @return iterable<string, array{string[], array<string, string>, int}>
     */
    public static function failingMetadataProvider(): iterable
    {
        yield 'single missing field' => [['author'], [], 1];
        yield 'all fields missing' => [['author', 'title', 'date'], [], 3];
        yield 'empty metadata with required fields' => [['author', 'title'], [], 2];
    }
}
