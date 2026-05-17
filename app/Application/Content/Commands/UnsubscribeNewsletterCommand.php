<?php

namespace App\Application\Content\Commands;

final class UnsubscribeNewsletterCommand
{
    public function __construct(
        public readonly string $token,
    ) {}
}
