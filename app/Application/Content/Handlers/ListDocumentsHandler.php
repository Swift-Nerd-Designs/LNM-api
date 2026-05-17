<?php

namespace App\Application\Content\Handlers;

use App\Application\Content\Queries\ListDocumentsQuery;
use App\Domain\Content\Document;
use App\Domain\Content\DocumentRepositoryInterface;

final class ListDocumentsHandler
{
    public function __construct(
        private readonly DocumentRepositoryInterface $documents,
    ) {}

    /** @return Document[] */
    public function handle(ListDocumentsQuery $query): array
    {
        return $this->documents->findAll($query->publishedOnly, $query->category);
    }
}
