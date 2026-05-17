<?php

namespace App\Application\Content\Handlers;

use App\Application\Content\Queries\ListSubscribersQuery;
use App\Domain\Content\NewsletterSubscriber;
use App\Domain\Content\NewsletterSubscriberRepositoryInterface;

final class ListSubscribersHandler
{
    public function __construct(
        private readonly NewsletterSubscriberRepositoryInterface $subscribers,
    ) {}

    /** @return NewsletterSubscriber[] */
    public function handle(ListSubscribersQuery $query): array
    {
        return $query->activeOnly
            ? $this->subscribers->findAllActive()
            : $this->subscribers->findAll();
    }
}
