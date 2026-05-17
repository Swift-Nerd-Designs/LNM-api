<?php

namespace App\Application\Content\Handlers;

use App\Application\Content\Commands\DeleteDocumentCommand;
use App\Domain\Content\DocumentRepositoryInterface;

final class DeleteDocumentHandler
{
    public function __construct(
        private readonly DocumentRepositoryInterface $documents,
    ) {}

    public function handle(DeleteDocumentCommand $cmd): void
    {
        if ($this->documents->findById($cmd->id) === null) {
            throw new \DomainException("Document #{$cmd->id} not found.");
        }

        $this->documents->delete($cmd->id);
    }
}
