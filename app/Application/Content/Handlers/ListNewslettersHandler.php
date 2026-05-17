<?php

namespace App\Application\Content\Handlers;

use App\Application\Content\Queries\ListNewslettersQuery;
use App\Domain\Content\Newsletter;
use App\Domain\Content\NewsletterRepositoryInterface;

final class ListNewslettersHandler
{
    public function __construct(
        private readonly NewsletterRepositoryInterface $newsletters,
    ) {}

    /** @return Newsletter[] */
    public function handle(ListNewslettersQuery $query): array
    {
        return $query->publishedOnly
            ? $this->newsletters->findPublished()
            : $this->newsletters->findAll();
    }
}
