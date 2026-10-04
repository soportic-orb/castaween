<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Auth;
use App\Core\View;
use App\Services\Pages;

final class PageController
{
    /** Pàgina de contingut del CMS. Els esborranys només els veu l'organització. */
    public function show(string $slug): void
    {
        $page = Pages::findBySlug($slug);
        $isDraft = $page !== null && (int) $page['published'] !== 1;

        if ($page === null || ($isDraft && Auth::user() === null)) {
            http_response_code(404);
            View::render('web/error', [
                'title'   => 'Pàgina no trobada',
                'code'    => 404,
                'message' => 'Aquesta pàgina no existeix o ja no està disponible.',
            ], 'layouts/public');
            return;
        }

        View::render('web/page', [
            'title'           => (string) $page['title'],
            'metaDescription' => trim((string) $page['summary']) !== '' ? (string) $page['summary'] : null,
            'page'            => $page,
            'bodyHtml'        => Pages::render($page),
            'isDraft'         => $isDraft,
        ], 'layouts/public');
    }
}
