<?php

namespace App\Application\Ports;

use App\Domain\Content\NewsletterSubscriber;

interface NewsletterSubscriptionMailerInterface
{
    public function sendConfirmation(NewsletterSubscriber $subscriber, string $confirmUrl): void;

    public function sendUnsubscribeConfirmation(NewsletterSubscriber $subscriber): void;
}
