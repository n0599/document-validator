<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator\Exception;

use LogicException;

class DuplicateRuleException extends LogicException implements DocumentValidatorExceptionInterface
{
}
