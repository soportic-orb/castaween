<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Core\Db;
use App\Core\Flash;
use App\Core\Logger;
use App\Core\Request;
use App\Core\Response;
use App\Core\Str;
use App\Core\Url;
use App\Core\Validator;
use App\Core\View;
use App\Services\Pages;

/** Pàgines de contingut (CMS) i textos legals. */
final class PageController
{
    public function index(): void
    {
        Pages::ensureDefaults();

        View::render('admin/pages', [
            'title'   => 'Pàgines',
            'pages'   => Db::all('SELECT * FROM `pages` ORDER BY `system_key` IS NOT NULL, `sort_order`, `id`'),
            'missing' => Pages::missingLegalData(),
        ], 'layouts/admin');
    }

    public function create(): void
    {
        View::render('admin/page_form', [
            'title'     => 'Nova pàgina',
            'page'      => $this->blank(),
            'errors'    => Flash::errors(),
            'variables' => Pages::variables(),
        ], 'layouts/admin');
        Flash::clearOld();
    }

    public function store(): void
    {
        $data = $this->validated(null);
        if ($data === null) {
            $this->create();
            return;
        }

        $id = Db::insert('pages', $data);
        Logger::audit('crea_pagina', (string) $id, ['titol' => $data['title']]);
        Flash::success('S\'ha creat la pàgina «' . $data['title'] . '».');
        Response::redirect(Url::to('/admin/pagines/' . $id));
    }

    public function edit(string $id): void
    {
        $page = $this->find((int) $id);

        View::render('admin/page_form', [
            'title'     => 'Editar · ' . $page['title'],
            'page'      => $page,
            'errors'    => Flash::errors(),
            'variables' => Pages::variables($page),
        ], 'layouts/admin');
        Flash::clearOld();
    }

    public function update(string $id): void
    {
        $page = $this->find((int) $id);
        $data = $this->validated($page);
        if ($data === null) {
            $this->edit($id);
            return;
        }

        Db::update('pages', $data, '`id` = :id', ['id' => $page['id']]);
        Logger::audit('edita_pagina', (string) $page['id'], ['titol' => $data['title']]);
        Flash::success('Canvis desats.');
        Response::redirect(Url::to('/admin/pagines/' . $page['id']));
    }

    public function toggle(string $id): void
    {
        $page = $this->find((int) $id);
        $published = (int) $page['published'] === 1 ? 0 : 1;
        Db::update('pages', ['published' => $published], '`id` = :id', ['id' => $page['id']]);

        Flash::success($published === 1
            ? 'S\'ha publicat «' . $page['title'] . '».'
            : '«' . $page['title'] . '» ha passat a esborrany i ja no es veu al web públic.');
        Response::redirect(Url::to('/admin/pagines'));
    }

    public function destroy(string $id): void
    {
        $page = $this->find((int) $id);

        if ($page['system_key'] !== null) {
            Flash::error('Les pàgines legals no es poden eliminar. Pots editar-ne el text.');
            Response::redirect(Url::to('/admin/pagines'));
        }

        Db::run('DELETE FROM `pages` WHERE `id` = :id', ['id' => $page['id']]);
        Logger::audit('elimina_pagina', (string) $page['id'], ['titol' => $page['title']]);
        Flash::success('S\'ha eliminat «' . $page['title'] . '».');
        Response::redirect(Url::to('/admin/pagines'));
    }

    // ----------------------------------------------------------------- Ajudants

    private function blank(): array
    {
        return [
            'id' => 0, 'slug' => '', 'system_key' => null, 'title' => '', 'summary' => '', 'body' => '',
            'cover_image' => null, 'published' => 0, 'show_in_menu' => 0, 'show_in_footer' => 0,
            'show_on_home' => 0,
            'sort_order' => (int) Db::value('SELECT COALESCE(MAX(`sort_order`), 0) + 1 FROM `pages` WHERE `system_key` IS NULL', [], 1),
        ];
    }

    private function find(int $id): array
    {
        $page = Db::first('SELECT * FROM `pages` WHERE `id` = :id', ['id' => $id]);
        if ($page === null) {
            Flash::error('No hem trobat aquesta pàgina.');
            Response::redirect(Url::to('/admin/pagines'));
        }
        return $page;
    }

    /** @return array<string, mixed>|null */
    private function validated(?array $current): ?array
    {
        $title = trim((string) Request::post('title', ''));
        $slug = Str::slug(trim((string) Request::post('slug', '')) ?: $title);

        $taken = $slug !== '' && (int) Db::value(
            'SELECT COUNT(*) FROM `pages` WHERE `slug` = :slug AND `id` <> :id',
            ['slug' => $slug, 'id' => (int) ($current['id'] ?? 0)],
            0
        ) > 0;

        $validator = Validator::make($_POST)
            ->required('title', 'Cal indicar el títol de la pàgina.')
            ->maxLen('title', 190, 'El títol és massa llarg.')
            ->maxLen('summary', 300, 'El resum ha de tenir 300 caràcters com a màxim.')
            ->check('slug', $slug !== '', 'L\'adreça només pot tenir lletres, números i guions.')
            ->check('slug', mb_strlen($slug) <= 120, 'L\'adreça és massa llarga.')
            ->check('slug', !$taken, 'Ja hi ha una altra pàgina amb aquesta adreça.');

        if ($validator->fails()) {
            Flash::setErrors($validator->errors());
            Flash::setOld($_POST);
            Flash::error('Revisa els camps marcats.');
            return null;
        }

        $data = [
            'title'          => $title,
            'slug'           => $slug,
            'summary'        => trim((string) Request::post('summary', '')) ?: null,
            'body'           => str_replace("\r\n", "\n", (string) Request::post('body', '')),
            'published'      => Request::post('published') ? 1 : 0,
            'show_in_menu'   => Request::post('show_in_menu') ? 1 : 0,
            'show_in_footer' => Request::post('show_in_footer') ? 1 : 0,
            'show_on_home'   => Request::post('show_on_home') ? 1 : 0,
            'sort_order'     => (int) Request::post('sort_order', 0),
        ];

        if (Request::post('remove_cover')) {
            $data['cover_image'] = null;
        }
        $cover = $this->storeCover();
        if ($cover !== null) {
            $data['cover_image'] = $cover;
        }

        return $data;
    }

    /** Puja la imatge de capçalera a public/uploads i en retorna la ruta pública. */
    private function storeCover(): ?string
    {
        $file = $_FILES['cover'] ?? null;
        if (!is_array($file) || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return null;
        }
        if ((int) $file['size'] > 6 * 1024 * 1024) {
            Flash::error('La imatge supera els 6 MB i no s\'ha desat.');
            return null;
        }

        $extension = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true) || @getimagesize($file['tmp_name']) === false) {
            Flash::error('La imatge ha de ser JPG, PNG o WEBP.');
            return null;
        }

        $dir = APP_ROOT . '/public/uploads';
        if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
            Flash::error('No es pot escriure al directori public/uploads.');
            return null;
        }

        $name = 'pagina-' . Str::token(6) . '.' . $extension;
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $name)) {
            Flash::error('No s\'ha pogut desar la imatge.');
            return null;
        }
        @chmod($dir . '/' . $name, 0644);

        return '/uploads/' . $name;
    }
}
