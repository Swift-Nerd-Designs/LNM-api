<?php

namespace App\Application\Content\Queries;

final class ListSubscribersQuery
{
    public function __construct(
        public readonly bool $activeOnly = true,
    ) {}
}
