<?php

namespace App\Infrastructure\Http\Controllers\Admin;

use App\Application\Content\Queries\ListSubscribersQuery;
use App\Infrastructure\Http\Controllers\BaseController;

class NewsletterSubscribers extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $activeOnly  = $this->request->getGet('active_only') !== '0';
        $subscribers = service('listSubscribersHandler')->handle(new ListSubscribersQuery($activeOnly));
        $count       = service('subscriberRepository')->countActive();

        return $this->ok([
            'subscribers'  => array_map(fn($s) => $s->toArray(), $subscribers),
            'active_count' => $count,
        ]);
    }

    public function delete(int $id): \CodeIgniter\HTTP\ResponseInterface
    {
        $db = \Config\Database::connect();

        $exists = $db->table('newsletter_subscribers')->where('id', $id)->countAllResults() > 0;
        if (!$exists) {
            return $this->notFound("Subscriber #{$id} not found.");
        }

        $db->table('newsletter_subscribers')->where('id', $id)->delete();

        return $this->ok();
    }
}
