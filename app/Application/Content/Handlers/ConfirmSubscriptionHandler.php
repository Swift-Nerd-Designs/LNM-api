<?php

namespace App\Application\Content\Handlers;

use App\Application\Content\Commands\ConfirmSubscriptionCommand;
use App\Domain\Content\NewsletterSubscriber;
use App\Domain\Content\NewsletterSubscriberRepositoryInterface;

final class ConfirmSubscriptionHandler
{
    public function __construct(
        private readonly NewsletterSubscriberRepositoryInterface $subscribers,
    ) {}

    public function handle(ConfirmSubscriptionCommand $cmd): NewsletterSubscriber
    {
        $subscriber = $this->subscribers->findByConfirmationToken($cmd->token);

        if ($subscriber === null) {
            throw new \DomainException('Invalid or expired confirmation token.');
        }

        $confirmed = new NewsletterSubscriber(
            id:                $subscriber->id,
            email:             $subscriber->email,
            name:              $subscriber->name,
            confirmationToken: null,
            unsubscribeToken:  $subscriber->unsubscribeToken,
            confirmed:         true,
            confirmedAt:       new \DateTimeImmutable(),
            subscribedAt:      $subscriber->subscribedAt,
            unsubscribedAt:    null,
        );

        return $this->subscribers->save($confirmed);
    }
}
