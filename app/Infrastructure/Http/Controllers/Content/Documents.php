<?php

namespace App\Infrastructure\Http\Controllers\Content;

use App\Application\Content\Queries\ListDocumentsQuery;
use App\Infrastructure\Http\Controllers\BaseController;

class Documents extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $category  = $this->request->getGet('category') ?: null;
        $documents = service('listDocumentsHandler')->handle(
            new ListDocumentsQuery(publishedOnly: true, category: $category)
        );

        return $this->ok([
            'documents' => array_map(fn($d) => $d->toArray(), $documents),
        ]);
    }
}
