<?php

declare(strict_types=1);

namespace Tests\Service\DocumentValidator;

use App\Service\DocumentValidator\ValidationError;
use App\Service\DocumentValidator\ValidationResult;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ValidationResultTest extends TestCase
{
    #[Test]
    public function it_is_valid_when_no_errors(): void
    {
        $result = new ValidationResult([]);

        $this->assertTrue($result->isValid());
    }

    #[Test]
    public function it_is_invalid_when_errors_exist(): void
    {
        $result = new ValidationResult([
            new ValidationError('error', []),
        ]);

        $this->assertFalse($result->isValid());
    }

    #[Test]
    public function it_returns_empty_errors_array(): void
    {
        $result = new ValidationResult([]);

        $this->assertSame([], $result->getErrors());
    }

    #[Test]
    public function it_returns_all_errors(): void
    {
        $errors = [
            new ValidationError('first', []),
            new ValidationError('second', []),
        ];

        $result = new ValidationResult($errors);

        $this->assertCount(2, $result->getErrors());
        $this->assertSame($errors, $result->getErrors());
    }
}
