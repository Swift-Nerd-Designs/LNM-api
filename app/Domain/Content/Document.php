<?php

namespace App\Domain\Content;

final class Document
{
    public function __construct(
        public readonly int                $id,
        public readonly string             $category,
        public readonly string             $title,
        public readonly ?string            $description,
        public readonly string             $filename,
        public readonly string             $fileUrl,
        public readonly int                $fileSize,
        public readonly bool               $published,
        public readonly \DateTimeImmutable $createdAt,
        public readonly \DateTimeImmutable $updatedAt,
    ) {}

    public static function fromArray(array $row): self
    {
        return new self(
            id:          (int) $row['id'],
            category:    $row['category'],
            title:       $row['title'],
            description: $row['description'] ?? null,
            filename:    $row['filename'],
            fileUrl:     $row['file_url'],
            fileSize:    (int) $row['file_size'],
            published:   (bool) $row['published'],
            createdAt:   new \DateTimeImmutable($row['created_at']),
            updatedAt:   new \DateTimeImmutable($row['updated_at']),
        );
    }

    public function toArray(): array
    {
        return [
            'id'          => $this->id,
            'category'    => $this->category,
            'title'       => $this->title,
            'description' => $this->description,
            'filename'    => $this->filename,
            'file_url'    => $this->fileUrl,
            'file_size'   => $this->fileSize,
            'published'   => $this->published,
            'created_at'  => $this->createdAt->format('Y-m-d H:i:s'),
            'updated_at'  => $this->updatedAt->format('Y-m-d H:i:s'),
        ];
    }
}
