<?php

namespace App\Application\Content\Queries;

final class ListDocumentsQuery
{
    public function __construct(
        public readonly bool    $publishedOnly = false,
        public readonly ?string $category      = null,
    ) {}
}
