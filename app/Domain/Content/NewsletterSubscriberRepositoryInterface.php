<?php

namespace App\Domain\Content;

interface NewsletterSubscriberRepositoryInterface
{
    public function findByEmail(string $email): ?NewsletterSubscriber;

    public function findByConfirmationToken(string $token): ?NewsletterSubscriber;

    public function findByUnsubscribeToken(string $token): ?NewsletterSubscriber;

    /** @return NewsletterSubscriber[] */
    public function findAll(): array;

    /** @return NewsletterSubscriber[] */
    public function findAllActive(): array;

    public function countActive(): int;

    public function save(NewsletterSubscriber $subscriber): NewsletterSubscriber;

    public function unsubscribeByToken(string $token): bool;
}
