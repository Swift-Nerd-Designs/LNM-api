<?php

namespace App\Application\Content\Handlers;

use App\Application\Content\Commands\SaveNewsletterCommand;
use App\Domain\Content\Newsletter;
use App\Domain\Content\NewsletterRepositoryInterface;

final class SaveNewsletterHandler
{
    public function __construct(
        private readonly NewsletterRepositoryInterface $newsletters,
    ) {}

    public function handle(SaveNewsletterCommand $cmd): Newsletter
    {
        $now = new \DateTimeImmutable();

        $existing = $cmd->id !== null ? $this->newsletters->findById($cmd->id) : null;

        $newsletter = new Newsletter(
            id:            $cmd->id ?? 0,
            issue:         $cmd->issue,
            title:         $cmd->title,
            description:   $cmd->description,
            filename:      $cmd->filename,
            fileUrl:       $cmd->fileUrl,
            fileSize:      $cmd->fileSize,
            published:     $cmd->published,
            publishedDate: $cmd->publishedDate,
            createdAt:     $existing?->createdAt ?? $now,
            updatedAt:     $now,
        );

        return $this->newsletters->save($newsletter);
    }
}
