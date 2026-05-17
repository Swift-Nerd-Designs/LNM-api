<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Content\Newsletter;
use App\Domain\Content\NewsletterRepositoryInterface;

class MySqlNewsletterRepository extends AbstractMysqlRepository implements NewsletterRepositoryInterface
{
    public function findAll(): array
    {
        $rows = $this->db->table('newsletters')
            ->orderBy('published_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        return array_map(fn($r) => Newsletter::fromArray($r), $rows);
    }

    public function findById(int $id): ?Newsletter
    {
        $row = $this->db->table('newsletters')->where('id', $id)->get()->getRowArray();
        return $row ? Newsletter::fromArray($row) : null;
    }

    public function findPublished(): array
    {
        $rows = $this->db->table('newsletters')
            ->where('published', 1)
            ->orderBy('published_date', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()->getResultArray();

        return array_map(fn($r) => Newsletter::fromArray($r), $rows);
    }

    public function save(Newsletter $newsletter): Newsletter
    {
        $payload = [
            'issue'          => $newsletter->issue,
            'title'          => $newsletter->title,
            'description'    => $newsletter->description,
            'filename'       => $newsletter->filename,
            'file_url'       => $newsletter->fileUrl,
            'file_size'      => $newsletter->fileSize,
            'published'      => $newsletter->published ? 1 : 0,
            'published_date' => $newsletter->publishedDate,
            'updated_at'     => $this->now(),
        ];

        if ($newsletter->id === 0) {
            $payload['created_at'] = $this->now();
            $this->db->table('newsletters')->insert($payload);
            $id = (int) $this->db->insertID();
        } else {
            $this->db->table('newsletters')->where('id', $newsletter->id)->update($payload);
            $id = $newsletter->id;
        }

        return $this->findById($id);
    }

    public function delete(int $id): void
    {
        $this->db->table('newsletters')->where('id', $id)->delete();
    }
}
