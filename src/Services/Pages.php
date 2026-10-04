<?php
declare(strict_types=1);

namespace App\Services;

use App\Core\Db;
use App\Core\Logger;
use App\Core\Markdown;
use App\Core\Settings;
use App\Core\Url;

/**
 * Pàgines de contingut (CMS): consulta, pàgines per defecte i presentació.
 */
final class Pages
{
    /** Claus de les pàgines legals que el web enllaça sempre. */
    public const LEGAL_NOTICE = 'legal_notice';
    public const PRIVACY      = 'privacy';
    public const TERMS        = 'terms';
    public const COOKIES      = 'cookies';

    private static bool $defaultsChecked = false;

    /** @var array<int, array>|null */
    private static ?array $publishedCache = null;

    public static function available(): bool
    {
        try {
            return Db::tableExists('pages');
        } catch (\Throwable) {
            return false;
        }
    }

    /**
     * Crea les pàgines per defecte que encara no existeixin. Les legals es
     * publiquen de seguida; la de mostra del túnel queda com a esborrany.
     */
    public static function ensureDefaults(): void
    {
        if (self::$defaultsChecked || !self::available()) {
            return;
        }
        self::$defaultsChecked = true;

        try {
            $existingKeys = array_column(Db::all('SELECT `system_key` FROM `pages` WHERE `system_key` IS NOT NULL'), 'system_key');
            $existingSlugs = array_column(Db::all('SELECT `slug` FROM `pages`'), 'slug');

            foreach (DefaultPages::all() as $page) {
                $key = $page['system_key'] ?? null;
                if ($key !== null ? in_array($key, $existingKeys, true) : in_array($page['slug'], $existingSlugs, true)) {
                    continue;
                }
                if ($key === null && Settings::get('pages_examples_seeded') === '1') {
                    continue;
                }

                $slug = $page['slug'];
                while (in_array($slug, $existingSlugs, true)) {
                    $slug .= '-2';
                }
                $page['slug'] = $slug;
                Db::insert('pages', $page);
                $existingSlugs[] = $slug;
            }

            // La pàgina de mostra només es crea una vegada: si l'eliminen, no torna.
            if (Settings::get('pages_examples_seeded') !== '1') {
                Settings::set('pages_examples_seeded', '1');
            }
            self::$publishedCache = null;
        } catch (\Throwable $e) {
            Logger::exception($e, 'Pàgines per defecte');
        }
    }

    /** @return array<int, array> Pàgines publicades, en ordre. */
    public static function published(): array
    {
        if (self::$publishedCache !== null) {
            return self::$publishedCache;
        }
        self::ensureDefaults();

        try {
            self::$publishedCache = self::available()
                ? Db::all('SELECT * FROM `pages` WHERE `published` = 1 ORDER BY `sort_order`, `id`')
                : [];
        } catch (\Throwable) {
            self::$publishedCache = [];
        }

        return self::$publishedCache;
    }

    /** @return array<int, array> */
    public static function menu(): array
    {
        return array_values(array_filter(self::published(), static fn (array $p) => (int) $p['show_in_menu'] === 1));
    }

    /** @return array<int, array> */
    public static function footer(): array
    {
        return array_values(array_filter(self::published(), static fn (array $p) => (int) $p['show_in_footer'] === 1));
    }

    /** @return array<int, array> */
    public static function home(): array
    {
        return array_values(array_filter(self::published(), static fn (array $p) => (int) $p['show_on_home'] === 1));
    }

    public static function findBySlug(string $slug): ?array
    {
        self::ensureDefaults();
        if (!self::available()) {
            return null;
        }
        return Db::first('SELECT * FROM `pages` WHERE `slug` = :slug', ['slug' => $slug]);
    }

    public static function findBySystemKey(string $key): ?array
    {
        foreach (self::published() as $page) {
            if ($page['system_key'] === $key) {
                return $page;
            }
        }
        return null;
    }

    public static function url(array $page): string
    {
        return Url::to('/pagina/' . $page['slug']);
    }

    /**
     * Adreça d'una pàgina legal. Si no està publicada, es fa servir la pàgina
     * d'informació general perquè l'enllaç no quedi mai trencat.
     */
    public static function urlFor(string $systemKey): string
    {
        $page = self::findBySystemKey($systemKey);
        return $page !== null ? self::url($page) : Url::to('/informacio');
    }

    /** Converteix el cos d'una pàgina en HTML, amb els marcadors substituïts. */
    public static function render(array $page): string
    {
        $html = Markdown::render((string) ($page['body'] ?? ''));
        $vars = self::variables($page);

        return preg_replace_callback(
            '/\{\{\s*([a-z0-9_]+)\s*\}\}/',
            static function (array $m) use ($vars): string {
                if (!array_key_exists($m[1], $vars)) {
                    return $m[0];
                }
                [$value, $isHtml, $label] = $vars[$m[1]];
                if ($value === '') {
                    return '<mark class="pending">[' . e($label) . ' pendent]</mark>';
                }
                return $isHtml ? $value : e($value);
            },
            $html
        ) ?? $html;
    }

    /**
     * Marcadors disponibles a les pàgines. Cada valor és
     * [text, ja és HTML?, nom per a l'avís quan falta].
     *
     * @return array<string, array{0:string,1:bool,2:string}>
     */
    public static function variables(?array $page = null): array
    {
        $place = trim((string) Settings::get('event_location') . ' ' . (string) Settings::get('event_city'));
        $holder = trim((string) Settings::get('legal_holder')) ?: (string) Settings::get('event_organizer');
        $email = trim((string) Settings::get('legal_email')) ?: (string) Settings::get('event_contact_email');
        $updated = $page !== null && !empty($page['updated_at']) ? date('d/m/Y', (int) strtotime((string) $page['updated_at'])) : date('d/m/Y');

        return [
            'titular'              => [$holder, false, 'Titular'],
            'nif'                  => [trim((string) Settings::get('legal_nif')), false, 'NIF'],
            'domicili'             => [trim((string) Settings::get('legal_address')), false, 'Domicili'],
            'registre'             => [trim((string) Settings::get('legal_registry')), false, 'Número de registre'],
            'email_contacte'       => [$email, false, 'Correu de contacte'],
            'telefon_contacte'     => [trim((string) Settings::get('event_contact_phone')), false, 'Telèfon'],
            'web'                  => [Url::host(), false, 'Domini'],
            'nom_esdeveniment'     => [(string) Settings::get('event_name'), false, 'Nom de l\'esdeveniment'],
            'data_esdeveniment'    => [(string) Settings::get('event_date_text'), false, 'Data'],
            'lloc_esdeveniment'    => [$place, false, 'Lloc'],
            'politica_anullacions' => [(string) Settings::get('cancellation_policy_text'), false, 'Política d\'anul·lacions'],
            'data_actualitzacio'   => [$updated, false, 'Data'],
            'taula_cookies'        => [self::cookieTable(), true, 'Taula de galetes'],
        ];
    }

    /** Galetes que fa servir el web, segons la configuració actual. */
    public static function cookies(): array
    {
        $rows = [
            ['ct_session', 'Pròpia', 'Tècnica (imprescindible)', 'Mantenir la sessió mentre fas la inscripció i protegir els formularis.', 'Fins que tanques el navegador'],
            ['ct_galetes', 'Pròpia', 'Tècnica (imprescindible)', 'Recordar si has acceptat o rebutjat les galetes opcionals.', '6 mesos'],
        ];

        if (self::analyticsId() !== '') {
            $rows[] = ['_ga', 'Google Analytics', 'Analítica (opcional)', 'Distingir visitants de manera anònima per elaborar estadístiques d\'ús.', '2 anys'];
            $rows[] = ['_ga_*', 'Google Analytics', 'Analítica (opcional)', 'Mantenir l\'estat de la visita per a les estadístiques.', '2 anys'];
        }

        return $rows;
    }

    public static function analyticsId(): string
    {
        $id = trim((string) Settings::get('google_analytics'));
        return preg_match('/^[A-Z0-9-]{4,40}$/i', $id) === 1 ? $id : '';
    }

    private static function cookieTable(): string
    {
        $html = '<div class="prose-table"><table><thead><tr><th>Nom</th><th>Titular</th><th>Tipus</th><th>Finalitat</th><th>Durada</th></tr></thead><tbody>';
        foreach (self::cookies() as $row) {
            $html .= '<tr><td><code>' . e($row[0]) . '</code></td><td>' . e($row[1]) . '</td><td>' . e($row[2])
                . '</td><td>' . e($row[3]) . '</td><td>' . e($row[4]) . '</td></tr>';
        }
        return $html . '</tbody></table></div>';
    }

    /** Dades legals que falten per completar (per a la llista del resum). */
    public static function missingLegalData(): array
    {
        $missing = [];
        foreach (['titular' => 'titular', 'nif' => 'NIF', 'domicili' => 'domicili', 'email_contacte' => 'correu de contacte'] as $key => $label) {
            if (self::variables()[$key][0] === '') {
                $missing[] = $label;
            }
        }
        return $missing;
    }
}
