<?php

declare(strict_types=1);

namespace Tests\Service\DocumentValidator\Rule;

use App\Service\DocumentValidator\Rule\MaxCharactersRule;
use App\Service\DocumentValidator\ValidationError;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MaxCharactersRuleTest extends TestCase
{
    #[Test]
    #[DataProvider('passingContentProvider')]
    public function it_passes_validation(string $content, int $max): void
    {
        $rule = new MaxCharactersRule($max);

        $errors = $rule->validate($content, []);

        $this->assertEmpty($errors);
    }

    #[Test]
    #[DataProvider('failingContentProvider')]
    public function it_fails_validation(string $content, int $max): void
    {
        $rule = new MaxCharactersRule($max);

        $errors = $rule->validate($content, []);

        $this->assertCount(1, $errors);
    }

    #[Test]
    public function it_returns_validation_error_instance(): void
    {
        $rule = new MaxCharactersRule(5);

        $errors = $rule->validate('abcdef', []);

        $this->assertInstanceOf(ValidationError::class, $errors[0]);
    }

    #[Test]
    public function it_returns_error_message_with_limit(): void
    {
        $rule = new MaxCharactersRule(10);

        $errors = $rule->validate(str_repeat('x', 11), []);

        $this->assertSame('Content exceeds 10 characters', $errors[0]->message);
    }

    #[Test]
    public function it_returns_error_context_with_max_and_actual(): void
    {
        $rule = new MaxCharactersRule(5);

        $errors = $rule->validate('abcdefgh', []);

        $this->assertSame(5, $errors[0]->context['max']);
        $this->assertSame(8, $errors[0]->context['actual']);
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function passingContentProvider(): iterable
    {
        yield 'empty string' => ['', 10];
        yield 'content shorter than limit' => ['hello', 10];
        yield 'content length equals limit' => ['abcde', 5];
        yield 'zero limit with empty string' => ['', 0];
        yield 'multibyte characters within limit' => ['абв', 3];
        yield 'emoji within limit' => ['😀😁', 2];
    }

    /**
     * @return iterable<string, array{string, int}>
     */
    public static function failingContentProvider(): iterable
    {
        yield 'one character over limit' => ['abcdef', 5];
        yield 'significantly over limit' => [str_repeat('x', 100), 10];
        yield 'zero limit with non-empty string' => ['a', 0];
        yield 'multibyte characters over limit' => ['абвг', 3];
        yield 'emoji over limit' => ['😀😁😂', 2];
    }
}
