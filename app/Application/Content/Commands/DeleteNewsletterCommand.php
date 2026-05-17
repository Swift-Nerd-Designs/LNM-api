<?php

namespace App\Application\Content\Commands;

final class DeleteNewsletterCommand
{
    public function __construct(
        public readonly int $id,
    ) {}
}
