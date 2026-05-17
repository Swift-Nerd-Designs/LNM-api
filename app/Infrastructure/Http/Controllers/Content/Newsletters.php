<?php

namespace App\Infrastructure\Http\Controllers\Content;

use App\Application\Content\Queries\ListNewslettersQuery;
use App\Infrastructure\Http\Controllers\BaseController;

class Newsletters extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $newsletters = service('listNewslettersHandler')->handle(new ListNewslettersQuery(publishedOnly: true));

        return $this->ok([
            'newsletters' => array_map(fn($n) => $n->toArray(), $newsletters),
        ]);
    }
}
