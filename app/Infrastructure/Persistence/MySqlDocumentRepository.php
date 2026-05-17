<?php

namespace App\Infrastructure\Persistence;

use App\Domain\Content\Document;
use App\Domain\Content\DocumentRepositoryInterface;

class MySqlDocumentRepository extends AbstractMysqlRepository implements DocumentRepositoryInterface
{
    public function findAll(bool $publishedOnly = false, ?string $category = null): array
    {
        $builder = $this->db->table('documents')->orderBy('title', 'ASC');

        if ($publishedOnly) {
            $builder->where('published', 1);
        }

        if ($category !== null) {
            $builder->where('category', $category);
        }

        $rows = $builder->get()->getResultArray();
        return array_map(fn($r) => Document::fromArray($r), $rows);
    }

    public function findById(int $id): ?Document
    {
        $row = $this->db->table('documents')->where('id', $id)->get()->getRowArray();
        return $row ? Document::fromArray($row) : null;
    }

    public function save(Document $document): Document
    {
        $payload = [
            'category'    => $document->category,
            'title'       => $document->title,
            'description' => $document->description,
            'filename'    => $document->filename,
            'file_url'    => $document->fileUrl,
            'file_size'   => $document->fileSize,
            'published'   => $document->published ? 1 : 0,
            'updated_at'  => $this->now(),
        ];

        if ($document->id === 0) {
            $payload['created_at'] = $this->now();
            $this->db->table('documents')->insert($payload);
            $id = (int) $this->db->insertID();
        } else {
            $this->db->table('documents')->where('id', $document->id)->update($payload);
            $id = $document->id;
        }

        return $this->findById($id);
    }

    public function delete(int $id): void
    {
        $this->db->table('documents')->where('id', $id)->delete();
    }
}
