<?php

declare(strict_types=1);

namespace App\Service\DocumentValidator\Exception;

use Exception;

class RuleNotFoundException extends Exception implements DocumentValidatorExceptionInterface
{
}
