<?php

namespace App\Application\Content\Commands;

final class ConfirmSubscriptionCommand
{
    public function __construct(
        public readonly string $token,
    ) {}
}
