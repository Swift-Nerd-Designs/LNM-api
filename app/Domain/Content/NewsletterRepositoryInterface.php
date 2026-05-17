<?php

namespace App\Domain\Content;

interface NewsletterRepositoryInterface
{
    /** @return Newsletter[] */
    public function findAll(): array;

    public function findById(int $id): ?Newsletter;

    /** @return Newsletter[] */
    public function findPublished(): array;

    public function save(Newsletter $newsletter): Newsletter;

    public function delete(int $id): void;
}
