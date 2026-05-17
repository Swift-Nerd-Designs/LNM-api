<?php

namespace App\Infrastructure\Http\Controllers\Admin;

use App\Application\Content\Commands\DeleteNewsletterCommand;
use App\Application\Content\Commands\SaveNewsletterCommand;
use App\Application\Content\Queries\ListNewslettersQuery;
use App\Infrastructure\Http\Controllers\BaseController;

class Newsletters extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $newsletters = service('listNewslettersHandler')->handle(new ListNewslettersQuery(publishedOnly: false));

        return $this->ok([
            'newsletters' => array_map(fn($n) => $n->toArray(), $newsletters),
        ]);
    }

    public function create(): \CodeIgniter\HTTP\ResponseInterface
    {
        $body = $this->jsonBody();

        [$error, $cmd] = $this->buildCommand(null, $body);
        if ($error) return $error;

        $newsletter = service('saveNewsletterHandler')->handle($cmd);

        return $this->json(['newsletter' => $newsletter->toArray()], 201);
    }

    public function update(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $body = $this->jsonBody();

        [$error, $cmd] = $this->buildCommand($id, $body);
        if ($error) return $error;

        try {
            $newsletter = service('saveNewsletterHandler')->handle($cmd);
        } catch (\DomainException $e) {
            return $this->notFound($e->getMessage());
        }

        return $this->ok(['newsletter' => $newsletter->toArray()]);
    }

    public function delete(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        try {
            service('deleteNewsletterHandler')->handle(new DeleteNewsletterCommand($id));
        } catch (\DomainException $e) {
            return $this->notFound($e->getMessage());
        }

        return $this->ok();
    }

    // ── Private helpers ───────────────────────────────────────────────

    private function buildCommand(?int $id, array $body): array
    {
        $issue = trim($body['issue'] ?? '');
        $title = trim($body['title'] ?? '');
        $fileUrl = trim($body['file_url'] ?? '');

        if ($issue === '') return [$this->error('issue is required.', 400), null];
        if ($title === '') return [$this->error('title is required.', 400), null];
        if ($fileUrl === '') return [$this->error('file_url is required.', 400), null];

        $cmd = new SaveNewsletterCommand(
            id:            $id,
            issue:         $issue,
            title:         $title,
            description:   trim($body['description'] ?? '') ?: null,
            filename:      trim($body['filename'] ?? ''),
            fileUrl:       $fileUrl,
            fileSize:      (int) ($body['file_size'] ?? 0),
            published:     !empty($body['published']),
            publishedDate: $body['published_date'] ?? null,
        );

        return [null, $cmd];
    }
}
