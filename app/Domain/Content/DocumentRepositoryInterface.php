<?php

namespace App\Domain\Content;

interface DocumentRepositoryInterface
{
    /** @return Document[] */
    public function findAll(bool $publishedOnly = false, ?string $category = null): array;

    public function findById(int $id): ?Document;

    public function save(Document $document): Document;

    public function delete(int $id): void;
}
