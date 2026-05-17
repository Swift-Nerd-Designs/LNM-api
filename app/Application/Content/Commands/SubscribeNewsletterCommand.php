<?php

namespace App\Application\Content\Commands;

final class SubscribeNewsletterCommand
{
    public function __construct(
        public readonly string  $email,
        public readonly ?string $name,
        public readonly string  $confirmUrlBase,
    ) {}
}
