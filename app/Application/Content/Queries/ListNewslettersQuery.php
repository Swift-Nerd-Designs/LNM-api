<?php

namespace App\Application\Content\Queries;

final class ListNewslettersQuery
{
    public function __construct(
        public readonly bool $publishedOnly = false,
    ) {}
}
