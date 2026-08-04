<?php

declare(strict_types=1);

namespace Tests\Service\DocumentValidator\Rule;

use App\Service\DocumentValidator\Rule\ProhibitedWordsRule;
use App\Service\DocumentValidator\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ProhibitedWordsRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('passingContentProvider')]
    public function it_passes_validation(string $content, array $words): void
    {
        $rule = new ProhibitedWordsRule($words);

        $errors = $rule->validate($content, []);

        $this->assertEmpty($errors);
    }

    #[Test]
    #[DataProvider('failingContentProvider')]
    public function it_fails_validation(string $content, array $words, int $expectedErrorCount): void
    {
        $rule = new ProhibitedWordsRule($words);

        $errors = $rule->validate($content, []);

        $this->assertCount($expectedErrorCount, $errors);
    }

    #[Test]
    public function it_returns_validation_error_instance(): void
    {
        $rule = new ProhibitedWordsRule(['spam']);

        $errors = $rule->validate('this is spam', []);

        $this->assertInstanceOf(ValidationError::class, $errors[0]);
    }

    #[Test]
    public function it_returns_error_message_with_prohibited_word(): void
    {
        $rule = new ProhibitedWordsRule(['spam']);

        $errors = $rule->validate('this is spam', []);

        $this->assertSame('Content contains prohibited word: spam', $errors[0]->message);
    }

    #[Test]
    public function it_returns_error_context_with_word(): void
    {
        $rule = new ProhibitedWordsRule(['spam']);

        $errors = $rule->validate('this is spam', []);

        $this->assertSame('spam', $errors[0]->context['word']);
    }

    #[Test]
    public function it_returns_separate_error_for_each_prohibited_word(): void
    {
        $rule = new ProhibitedWordsRule(['spam', 'viagra']);

        $errors = $rule->validate('spam and viagra here', []);

        $this->assertSame('spam', $errors[0]->context['word']);
        $this->assertSame('viagra', $errors[1]->context['word']);
    }

    /**
     * @return iterable<string, array{string, string[]}>
     */
    public static function passingContentProvider(): iterable
    {
        yield 'content without prohibited words' => ['hello world', ['spam', 'viagra']];
        yield 'empty content' => ['', ['spam']];
        yield 'empty word list' => ['any content here', []];
        yield 'empty content and empty word list' => ['', []];
    }

    /**
     * @return iterable<string, array{string, string[], int}>
     */
    public static function failingContentProvider(): iterable
    {
        yield 'single prohibited word' => ['this is spam', ['spam'], 1];
        yield 'multiple prohibited words' => ['spam and viagra', ['spam', 'viagra'], 2];
        yield 'duplicate occurrence produces one error' => ['spam spam spam', ['spam'], 1];
        yield 'case insensitive uppercase' => ['SPAM', ['spam'], 1];
        yield 'case insensitive mixed' => ['SpAm', ['spam'], 1];
        yield 'prohibited word as part of another word' => ['antispam filter', ['spam'], 1];
    }
}
