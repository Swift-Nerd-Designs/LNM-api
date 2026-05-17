<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Content\NewsletterSubscriber;
use App\Domain\Content\NewsletterSubscriberRepositoryInterface;

class MySqlNewsletterSubscriberRepository extends AbstractMysqlRepository implements NewsletterSubscriberRepositoryInterface
{
    public function findByEmail(string $email): ?NewsletterSubscriber
    {
        $row = $this->db->table('newsletter_subscribers')
            ->where('email', $email)
            ->get()->getRowArray();

        return $row ? NewsletterSubscriber::fromArray($row) : null;
    }

    public function findByConfirmationToken(string $token): ?NewsletterSubscriber
    {
        $row = $this->db->table('newsletter_subscribers')
            ->where('confirmation_token', $token)
            ->get()->getRowArray();

        return $row ? NewsletterSubscriber::fromArray($row) : null;
    }

    public function findByUnsubscribeToken(string $token): ?NewsletterSubscriber
    {
        $row = $this->db->table('newsletter_subscribers')
            ->where('unsubscribe_token', $token)
            ->get()->getRowArray();

        return $row ? NewsletterSubscriber::fromArray($row) : null;
    }

    public function findAll(): array
    {
        $rows = $this->db->table('newsletter_subscribers')
            ->orderBy('subscribed_at', 'DESC')
            ->get()->getResultArray();

        return array_map(fn($r) => NewsletterSubscriber::fromArray($r), $rows);
    }

    public function findAllActive(): array
    {
        $rows = $this->db->table('newsletter_subscribers')
            ->where('confirmed', 1)
            ->where('unsubscribed_at IS NULL', null, false)
            ->orderBy('subscribed_at', 'DESC')
            ->get()->getResultArray();

        return array_map(fn($r) => NewsletterSubscriber::fromArray($r), $rows);
    }

    public function countActive(): int
    {
        return $this->db->table('newsletter_subscribers')
            ->where('confirmed', 1)
            ->where('unsubscribed_at IS NULL', null, false)
            ->countAllResults();
    }

    public function save(NewsletterSubscriber $subscriber): NewsletterSubscriber
    {
        $payload = [
            'email'             => $subscriber->email,
            'name'              => $subscriber->name,
            'confirmation_token'=> $subscriber->confirmationToken,
            'unsubscribe_token' => $subscriber->unsubscribeToken,
            'confirmed'         => $subscriber->confirmed ? 1 : 0,
            'confirmed_at'      => $subscriber->confirmedAt?->format('Y-m-d H:i:s'),
            'subscribed_at'     => $subscriber->subscribedAt->format('Y-m-d H:i:s'),
            'unsubscribed_at'   => $subscriber->unsubscribedAt?->format('Y-m-d H:i:s'),
        ];

        if ($subscriber->id === 0) {
            $this->db->table('newsletter_subscribers')->insert($payload);
            $id = (int) $this->db->insertID();
        } else {
            $this->db->table('newsletter_subscribers')->where('id', $subscriber->id)->update($payload);
            $id = $subscriber->id;
        }

        return $this->findById($id);
    }

    public function unsubscribeByToken(string $token): bool
    {
        $this->db->table('newsletter_subscribers')
            ->where('unsubscribe_token', $token)
            ->update(['unsubscribed_at' => $this->now()]);

        return $this->db->affectedRows() > 0;
    }

    private function findById(int $id): NewsletterSubscriber
    {
        $row = $this->db->table('newsletter_subscribers')->where('id', $id)->get()->getRowArray();
        return NewsletterSubscriber::fromArray($row);
    }
}
