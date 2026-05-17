<?php

namespace App\Infrastructure\Services;

use App\Application\Ports\NewsletterSubscriptionMailerInterface;
use App\Domain\Content\NewsletterSubscriber;
use App\Domain\Core\SettingsRepositoryInterface;

class NewsletterSubscriptionMailer implements NewsletterSubscriptionMailerInterface
{
    public function __construct(
        private readonly SettingsRepositoryInterface $settings,
    ) {}

    public function sendConfirmation(NewsletterSubscriber $subscriber, string $confirmUrl): void
    {
        $apiKey = env('RESEND_API_KEY', '');
        if ($apiKey === '') return;

        [$siteName, $fromEmail] = $this->siteIdentity();
        if ($fromEmail === '') return;

        $name    = $subscriber->name ?? 'Subscriber';
        $subject = "Confirm your subscription — {$siteName}";

        $this->post($apiKey, [
            'from'    => "{$siteName} <{$fromEmail}>",
            'to'      => [$subscriber->email],
            'subject' => $subject,
            'html'    => $this->buildConfirmationHtml($name, $confirmUrl, $siteName),
        ]);
    }

    public function sendUnsubscribeConfirmation(NewsletterSubscriber $subscriber): void
    {
        $apiKey = env('RESEND_API_KEY', '');
        if ($apiKey === '') return;

        [$siteName, $fromEmail] = $this->siteIdentity();
        if ($fromEmail === '') return;

        $name    = $subscriber->name ?? 'Subscriber';
        $subject = "You've been unsubscribed — {$siteName}";

        $this->post($apiKey, [
            'from'    => "{$siteName} <{$fromEmail}>",
            'to'      => [$subscriber->email],
            'subject' => $subject,
            'html'    => $this->buildUnsubscribeHtml($name, $siteName),
        ]);
    }

    // ── Private helpers ───────────────────────────────────────────────

    /** @return array{string, string} [siteName, fromEmail] */
    private function siteIdentity(): array
    {
        $siteName  = $this->settings->get('site_name', 'Our Website');
        $fromEmail = $this->settings->get('contact_email', '');
        return [$siteName, $fromEmail];
    }

    private function post(string $apiKey, array $payload): void
    {
        $ch = curl_init('https://api.resend.com/emails');
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json',
            ],
        ]);
        $response = curl_exec($ch);
        $status   = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($status >= 400) {
            log_message('error', "NewsletterSubscriptionMailer failed [{$status}]: {$response}");
        }
    }

    private function e(string $s): string
    {
        return htmlspecialchars($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    private function buildConfirmationHtml(string $name, string $confirmUrl, string $siteName): string
    {
        $eName       = $this->e($name);
        $eSiteName   = $this->e($siteName);
        $eConfirmUrl = $this->e($confirmUrl);
        $year        = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Confirm your subscription</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f4f4f5;">
  <tr><td align="center" style="padding:40px 16px;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="560"
      style="max-width:560px;width:100%;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

      <tr><td style="background-color:#1a1a1a;padding:28px 32px;text-align:center;">
        <p style="margin:0;font-size:11px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:#9ca3af;">Newsletter</p>
        <h1 style="margin:6px 0 0;font-size:22px;font-weight:700;color:#ffffff;line-height:1.3;">Confirm your subscription</h1>
      </td></tr>

      <tr><td style="padding:32px 32px 24px;">
        <p style="margin:0 0 16px;font-size:15px;color:#374151;line-height:1.7;">Hi {$eName},</p>
        <p style="margin:0 0 24px;font-size:15px;color:#374151;line-height:1.7;">
          Thanks for signing up to the <strong>{$eSiteName}</strong> newsletter.
          Click the button below to confirm your email address and complete your subscription.
        </p>
        <table role="presentation" border="0" cellpadding="0" cellspacing="0">
          <tr><td style="border-radius:8px;background-color:#1a1a1a;">
            <a href="{$eConfirmUrl}"
               style="display:inline-block;padding:14px 28px;font-size:15px;font-weight:600;color:#ffffff;text-decoration:none;border-radius:8px;">
              Confirm subscription
            </a>
          </td></tr>
        </table>
        <p style="margin:24px 0 0;font-size:12px;color:#9ca3af;line-height:1.6;">
          Or copy this link into your browser:<br>
          <a href="{$eConfirmUrl}" style="color:#6b7280;word-break:break-all;">{$eConfirmUrl}</a>
        </p>
        <p style="margin:16px 0 0;font-size:12px;color:#9ca3af;line-height:1.6;">
          If you didn't request this, you can safely ignore this email.
        </p>
      </td></tr>

      <tr><td style="background-color:#f9fafb;border-top:1px solid #e5e7eb;padding:16px 32px;">
        <p style="margin:0;font-size:11px;color:#9ca3af;">&copy; {$year} {$eSiteName}. All rights reserved.</p>
      </td></tr>

    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }

    private function buildUnsubscribeHtml(string $name, string $siteName): string
    {
        $eName     = $this->e($name);
        $eSiteName = $this->e($siteName);
        $year      = date('Y');

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>You've been unsubscribed</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f4f5;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;">
<table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f4f4f5;">
  <tr><td align="center" style="padding:40px 16px;">
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="560"
      style="max-width:560px;width:100%;background-color:#ffffff;border-radius:12px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,0.08);">

      <tr><td style="background-color:#1a1a1a;padding:28px 32px;text-align:center;">
        <p style="margin:0;font-size:11px;font-weight:600;letter-spacing:1.5px;text-transform:uppercase;color:#9ca3af;">Newsletter</p>
        <h1 style="margin:6px 0 0;font-size:22px;font-weight:700;color:#ffffff;line-height:1.3;">You've been unsubscribed</h1>
      </td></tr>

      <tr><td style="padding:32px 32px 24px;">
        <p style="margin:0 0 16px;font-size:15px;color:#374151;line-height:1.7;">Hi {$eName},</p>
        <p style="margin:0 0 0;font-size:15px;color:#374151;line-height:1.7;">
          You've been successfully removed from the <strong>{$eSiteName}</strong> newsletter.
          You won't receive any further emails from us.
        </p>
        <p style="margin:16px 0 0;font-size:13px;color:#9ca3af;line-height:1.6;">
          Changed your mind? You can re-subscribe at any time from our website.
        </p>
      </td></tr>

      <tr><td style="background-color:#f9fafb;border-top:1px solid #e5e7eb;padding:16px 32px;">
        <p style="margin:0;font-size:11px;color:#9ca3af;">&copy; {$year} {$eSiteName}. All rights reserved.</p>
      </td></tr>

    </table>
  </td></tr>
</table>
</body>
</html>
HTML;
    }
}
