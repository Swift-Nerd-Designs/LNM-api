<?php

namespace App\Application\Content\Handlers;

use App\Application\Content\Commands\SaveDocumentCommand;
use App\Domain\Content\Document;
use App\Domain\Content\DocumentRepositoryInterface;

final class SaveDocumentHandler
{
    public function __construct(
        private readonly DocumentRepositoryInterface $documents,
    ) {}

    public function handle(SaveDocumentCommand $cmd): Document
    {
        $now = new \DateTimeImmutable();

        $existing = $cmd->id !== null ? $this->documents->findById($cmd->id) : null;

        $document = new Document(
            id:          $cmd->id ?? 0,
            category:    $cmd->category,
            title:       $cmd->title,
            description: $cmd->description,
            filename:    $cmd->filename,
            fileUrl:     $cmd->fileUrl,
            fileSize:    $cmd->fileSize,
            published:   $cmd->published,
            createdAt:   $existing?->createdAt ?? $now,
            updatedAt:   $now,
        );

        return $this->documents->save($document);
    }
}
