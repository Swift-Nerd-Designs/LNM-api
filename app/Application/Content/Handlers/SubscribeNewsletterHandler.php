<?php

namespace App\Application\Content\Handlers;

use App\Application\Content\Commands\SubscribeNewsletterCommand;
use App\Application\Ports\NewsletterSubscriptionMailerInterface;
use App\Domain\Content\NewsletterSubscriber;
use App\Domain\Content\NewsletterSubscriberRepositoryInterface;

final class SubscribeNewsletterHandler
{
    public function __construct(
        private readonly NewsletterSubscriberRepositoryInterface $subscribers,
        private readonly NewsletterSubscriptionMailerInterface   $mailer,
    ) {}

    /**
     * Returns: 'subscribed' | 'already_active' | 'confirmation_resent'
     */
    public function handle(SubscribeNewsletterCommand $cmd): string
    {
        $existing = $this->subscribers->findByEmail($cmd->email);

        if ($existing !== null) {
            if ($existing->isActive()) {
                return 'already_active';
            }

            // Resend confirmation for unconfirmed or previously unsubscribed
            $token = $this->generateToken();
            $saved = $this->saveSubscriber($existing, $cmd->name, $token, false);
            $confirmUrl = rtrim($cmd->confirmUrlBase, '/') . '?token=' . $token;
            $this->mailer->sendConfirmation($saved, $confirmUrl);

            return 'confirmation_resent';
        }

        $token = $this->generateToken();
        $subscriber = new NewsletterSubscriber(
            id:                0,
            email:             $cmd->email,
            name:              $cmd->name,
            confirmationToken: $token,
            unsubscribeToken:  $this->generateToken(),
            confirmed:         false,
            confirmedAt:       null,
            subscribedAt:      new \DateTimeImmutable(),
            unsubscribedAt:    null,
        );

        $saved = $this->subscribers->save($subscriber);
        $confirmUrl = rtrim($cmd->confirmUrlBase, '/') . '?token=' . $token;
        $this->mailer->sendConfirmation($saved, $confirmUrl);

        return 'subscribed';
    }

    private function saveSubscriber(
        NewsletterSubscriber $existing,
        ?string $name,
        string $confirmationToken,
        bool $confirmed,
    ): NewsletterSubscriber {
        $updated = new NewsletterSubscriber(
            id:                $existing->id,
            email:             $existing->email,
            name:              $name ?? $existing->name,
            confirmationToken: $confirmationToken,
            unsubscribeToken:  $existing->unsubscribeToken,
            confirmed:         $confirmed,
            confirmedAt:       null,
            subscribedAt:      $existing->subscribedAt,
            unsubscribedAt:    null,
        );

        return $this->subscribers->save($updated);
    }

    private function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}
