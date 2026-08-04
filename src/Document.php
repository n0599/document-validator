<?php

declare(strict_types=1);

namespace App;

readonly class Document
{
    /**
     * @param int                   $id
     * @param int                   $tenantId
     * @param string                $content
     * @param array<string, string> $metadata
     */
    public function __construct(
        public int $id,
        public int $tenantId,
        public string $content,
        public array $metadata,
    ) {}
}
