<?php

namespace App\Domain\Content;

final class Newsletter
{
    public function __construct(
        public readonly int                $id,
        public readonly string             $issue,
        public readonly string             $title,
        public readonly ?string            $description,
        public readonly string             $filename,
        public readonly string             $fileUrl,
        public readonly int                $fileSize,
        public readonly bool               $published,
        public readonly ?string            $publishedDate,
        public readonly \DateTimeImmutable $createdAt,
        public readonly \DateTimeImmutable $updatedAt,
    ) {}

    public static function fromArray(array $row): self
    {
        return new self(
            id:            (int) $row['id'],
            issue:         $row['issue'],
            title:         $row['title'],
            description:   $row['description'] ?? null,
            filename:      $row['filename'],
            fileUrl:       $row['file_url'],
            fileSize:      (int) $row['file_size'],
            published:     (bool) $row['published'],
            publishedDate: $row['published_date'] ?? null,
            createdAt:     new \DateTimeImmutable($row['created_at']),
            updatedAt:     new \DateTimeImmutable($row['updated_at']),
        );
    }

    public function toArray(): array
    {
        return [
            'id'             => $this->id,
            'issue'          => $this->issue,
            'title'          => $this->title,
            'description'    => $this->description,
            'filename'       => $this->filename,
            'file_url'       => $this->fileUrl,
            'file_size'      => $this->fileSize,
            'published'      => $this->published,
            'published_date' => $this->publishedDate,
            'created_at'     => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at'     => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
