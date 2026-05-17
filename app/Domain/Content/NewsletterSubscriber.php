<?php

namespace App\Domain\Content;

final class NewsletterSubscriber
{
    public function __construct(
        public readonly int                 $id,
        public readonly string              $email,
        public readonly ?string             $name,
        public readonly ?string             $confirmationToken,
        public readonly string              $unsubscribeToken,
        public readonly bool                $confirmed,
        public readonly ?\DateTimeImmutable $confirmedAt,
        public readonly \DateTimeImmutable  $subscribedAt,
        public readonly ?\DateTimeImmutable $unsubscribedAt,
    ) {}

    public function isActive(): bool
    {
        return $this->confirmed && $this->unsubscribedAt === null;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            id:                (int) $row['id'],
            email:             $row['email'],
            name:              $row['name'] ?? null,
            confirmationToken: $row['confirmation_token'] ?? null,
            unsubscribeToken:  $row['unsubscribe_token'],
            confirmed:         (bool) $row['confirmed'],
            confirmedAt:       isset($row['confirmed_at'])    ? new \DateTimeImmutable($row['confirmed_at'])    : null,
            subscribedAt:      new \DateTimeImmutable($row['subscribed_at'] ?? 'now'),
            unsubscribedAt:    isset($row['unsubscribed_at']) ? new \DateTimeImmutable($row['unsubscribed_at']) : null,
        );
    }

    public function toArray(): array
    {
        return [
            'id'                 => $this->id,
            'email'              => $this->email,
            'name'               => $this->name,
            'confirmed'          => $this->confirmed,
            'confirmed_at'       => $this->confirmedAt?->format('Y-m-d H:i:s'),
            'subscribed_at'      => $this->subscribedAt->format('Y-m-d H:i:s'),
            'unsubscribed_at'    => $this->unsubscribedAt?->format('Y-m-d H:i:s'),
        ];
    }
}
