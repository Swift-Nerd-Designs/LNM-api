<?php

namespace App\Application\Content\Commands;

final class DeleteDocumentCommand
{
    public function __construct(
        public readonly int $id,
    ) {}
}
