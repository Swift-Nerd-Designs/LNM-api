<?php

namespace App\Infrastructure\Http\Controllers\Admin;

use App\Application\Content\Commands\DeleteDocumentCommand;
use App\Application\Content\Commands\SaveDocumentCommand;
use App\Application\Content\Queries\ListDocumentsQuery;
use App\Infrastructure\Http\Controllers\BaseController;

class Documents extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $category  = $this->request->getGet('category') ?: null;
        $documents = service('listDocumentsHandler')->handle(
            new ListDocumentsQuery(publishedOnly: false, category: $category)
        );

        return $this->ok([
            'documents' => array_map(fn($d) => $d->toArray(), $documents),
        ]);
    }

    public function create(): \CodeIgniter\HTTP\ResponseInterface
    {
        $body = $this->jsonBody();

        [$error, $cmd] = $this->buildCommand(null, $body);
        if ($error) return $error;

        $document = service('saveDocumentHandler')->handle($cmd);

        return $this->json(['document' => $document->toArray()], 201);
    }

    public function update(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $body = $this->jsonBody();

        [$error, $cmd] = $this->buildCommand($id, $body);
        if ($error) return $error;

        try {
            $document = service('saveDocumentHandler')->handle($cmd);
        } catch (\DomainException $e) {
            return $this->notFound($e->getMessage());
        }

        return $this->ok(['document' => $document->toArray()]);
    }

    public function delete(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        try {
            service('deleteDocumentHandler')->handle(new DeleteDocumentCommand($id));
        } catch (\DomainException $e) {
            return $this->notFound($e->getMessage());
        }

        return $this->ok();
    }

    // ── Private helpers ───────────────────────────────────────────────

    private function buildCommand(?int $id, array $body): array
    {
        $category = trim($body['category'] ?? '');
        $title    = trim($body['title']    ?? '');
        $fileUrl  = trim($body['file_url'] ?? '');

        if ($category === '') return [$this->error('category is required.', 400), null];
        if ($title    === '') return [$this->error('title is required.', 400), null];
        if ($fileUrl  === '') return [$this->error('file_url is required.', 400), null];

        $cmd = new SaveDocumentCommand(
            id:          $id,
            category:    $category,
            title:       $title,
            description: trim($body['description'] ?? '') ?: null,
            filename:    trim($body['filename'] ?? ''),
            fileUrl:     $fileUrl,
            fileSize:    (int) ($body['file_size'] ?? 0),
            published:   !empty($body['published']),
        );

        return [null, $cmd];
    }
}
