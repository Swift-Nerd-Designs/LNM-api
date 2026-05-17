<?php

namespace App\Infrastructure\Http\Controllers\Content;

use App\Application\Content\Commands\ConfirmSubscriptionCommand;
use App\Application\Content\Commands\SubscribeNewsletterCommand;
use App\Application\Content\Commands\UnsubscribeNewsletterCommand;
use App\Infrastructure\Http\Controllers\BaseController;

class NewsletterSubscriptions extends BaseController
{
    public function subscribe(): \CodeIgniter\HTTP\ResponseInterface
    {
        // Guard: feature toggle
        if (service('settingsRepository')->get('newsletters_enabled', '1') !== '1') {
            return $this->error('Newsletter subscriptions are currently unavailable.', 503);
        }

        $ip = $this->request->getIPAddress();
        if ($this->rateLimited("newsletter_subscribe:{$ip}", 5, 300)) {
            return $this->tooManyRequests('Too many subscription attempts. Please try again later.');
        }

        $body  = $this->jsonBody();
        $email = trim($body['email'] ?? '');
        $name  = trim($body['name']  ?? '') ?: null;

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('A valid email address is required.', 400);
        }

        $confirmUrlBase = base_url('newsletter/confirm');

        try {
            $status = service('subscribeNewsletterHandler')->handle(
                new SubscribeNewsletterCommand($email, $name, $confirmUrlBase)
            );
        } catch (\Exception $e) {
            log_message('error', 'NewsletterSubscriptions::subscribe — ' . $e->getMessage());
            return $this->error('Something went wrong. Please try again.', 500);
        }

        $message = match ($status) {
            'already_active'       => 'You are already subscribed.',
            'confirmation_resent'  => 'A new confirmation email has been sent. Please check your inbox.',
            default                => 'Almost there! Check your email to confirm your subscription.',
        };

        return $this->ok(['message' => $message, 'status' => $status]);
    }

    public function confirm(): \CodeIgniter\HTTP\ResponseInterface
    {
        $token = trim($this->request->getGet('token') ?? '');

        if ($token === '') {
            return $this->error('Missing confirmation token.', 400);
        }

        try {
            service('confirmSubscriptionHandler')->handle(new ConfirmSubscriptionCommand($token));
        } catch (\DomainException $e) {
            return $this->error($e->getMessage(), 400);
        }

        $frontendUrl = rtrim(env('FRONTEND_URL', base_url()), '/');
        return redirect()->to("{$frontendUrl}/newsletter/confirmed");
    }

    public function unsubscribe(): \CodeIgniter\HTTP\ResponseInterface
    {
        $token = trim($this->request->getGet('token') ?? '');

        if ($token === '') {
            return $this->error('Missing unsubscribe token.', 400);
        }

        try {
            service('unsubscribeNewsletterHandler')->handle(new UnsubscribeNewsletterCommand($token));
        } catch (\DomainException $e) {
            return $this->error($e->getMessage(), 400);
        }

        $frontendUrl = rtrim(env('FRONTEND_URL', base_url()), '/');
        return redirect()->to("{$frontendUrl}/newsletter/unsubscribed");
    }
}
