<?php

namespace App\Application\Content\Commands;

final class SaveDocumentCommand
{
    public function __construct(
        public readonly ?int    $id,
        public readonly string  $category,
        public readonly string  $title,
        public readonly ?string $description,
        public readonly string  $filename,
        public readonly string  $fileUrl,
        public readonly int     $fileSize,
        public readonly bool    $published,
    ) {}
}
