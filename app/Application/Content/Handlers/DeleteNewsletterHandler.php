<?php

namespace App\Application\Content\Handlers;

use App\Application\Content\Commands\DeleteNewsletterCommand;
use App\Domain\Content\NewsletterRepositoryInterface;

final class DeleteNewsletterHandler
{
    public function __construct(
        private readonly NewsletterRepositoryInterface $newsletters,
    ) {}

    public function handle(DeleteNewsletterCommand $cmd): void
    {
        if ($this->newsletters->findById($cmd->id) === null) {
            throw new \DomainException("Newsletter #{$cmd->id} not found.");
        }

        $this->newsletters->delete($cmd->id);
    }
}
