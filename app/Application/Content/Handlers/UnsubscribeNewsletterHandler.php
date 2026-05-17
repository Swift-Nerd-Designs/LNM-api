<?php

namespace App\Application\Content\Handlers;

use App\Application\Content\Commands\UnsubscribeNewsletterCommand;
use App\Application\Ports\NewsletterSubscriptionMailerInterface;
use App\Domain\Content\NewsletterSubscriberRepositoryInterface;

final class UnsubscribeNewsletterHandler
{
    public function __construct(
        private readonly NewsletterSubscriberRepositoryInterface $subscribers,
        private readonly NewsletterSubscriptionMailerInterface   $mailer,
    ) {}

    public function handle(UnsubscribeNewsletterCommand $cmd): void
    {
        $subscriber = $this->subscribers->findByUnsubscribeToken($cmd->token);

        if ($subscriber === null || !$subscriber->isActive()) {
            throw new \DomainException('Invalid unsubscribe token or already unsubscribed.');
        }

        $this->subscribers->unsubscribeByToken($cmd->token);

        // Reload to get unsubscribedAt populated for the email
        $updated = $this->subscribers->findByUnsubscribeToken($cmd->token);
        if ($updated !== null) {
            $this->mailer->sendUnsubscribeConfirmation($updated);
        }
    }
}
