<?php
declare(strict_types=1);

use App\Core\Money;
use App\Core\Settings;
use App\Core\Str;
use App\Core\Url;

/** Escapa text per a HTML. */
function e(mixed $value): string
{
    return Str::e($value === null ? '' : (string) $value);
}

/** URL interna de l'aplicació. */
function url(string $path = '/'): string
{
    return Url::to($path);
}

/** Recurs estàtic amb control de memòria cau. */
function asset(string $path): string
{
    return Url::asset($path);
}

/** Import en cèntims formatat com a text. */
function money(int $cents): string
{
    return Money::format($cents);
}

/** Valor de configuració. */
function setting(string $key, mixed $default = null): mixed
{
    return Settings::get($key, $default);
}

/** Data en format català curt. */
function dt(?string $value, string $format = 'd/m/Y H:i'): string
{
    if ($value === null || trim($value) === '') {
        return '—';
    }
    $ts = strtotime($value);
    return $ts === false ? '—' : date($format, $ts);
}

/** Marca «selected» o «checked» segons una condició. */
function selectedIf(bool $condition): string
{
    return $condition ? ' selected' : '';
}

function checkedIf(bool $condition): string
{
    return $condition ? ' checked' : '';
}

/** Camp d'un formulari recuperant el valor anterior després d'un error. */
function old(string $key, mixed $default = ''): string
{
    return e(App\Core\Flash::old($key, $default));
}

/**
 * Icones de la Castanyada terrorífica, dibuixades en SVG amb els colors del
 * Panell de Gestió (variables --pdsh-*). Són decoratives: porten aria-hidden.
 * Noms: pumpkin, chestnut, ghost, tunnel, candle, bat.
 */
function spooky_icon(string $name, string $class = 'spooky-icon'): string
{
    static $paths = [
        'pumpkin' => '<path d="M32 14c-2-6 1-10 6-11-3 3-4 6-3 11z" style="fill:var(--pdsh-olive)"/>'
            . '<ellipse cx="20" cy="38" rx="14" ry="20" style="fill:var(--pdsh-secondary)"/>'
            . '<ellipse cx="44" cy="38" rx="14" ry="20" style="fill:var(--pdsh-secondary)"/>'
            . '<ellipse cx="32" cy="38" rx="13" ry="21" style="fill:var(--pdsh-secondary)"/>'
            . '<ellipse cx="32" cy="38" rx="13" ry="21" fill="#fff" opacity=".18"/>'
            . '<path d="M20 33l6-6 4 6zM34 33l6-6 4 6z" style="fill:var(--pdsh-ink)"/>'
            . '<path d="M18 44c4 6 24 6 28 0l-4 2-3-3-3 4-4-4-4 4-3-4-3 3z" style="fill:var(--pdsh-ink)"/>',
        'chestnut' => '<path d="M32 8C18 20 10 32 12 44c2 10 12 14 20 14s18-4 20-14c2-12-6-24-20-36z" style="fill:var(--pdsh-olive)"/>'
            . '<path d="M13 44c6 6 32 6 38 0-1 9-10 14-19 14s-18-5-19-14z" style="fill:var(--pdsh-cream)" opacity=".7"/>'
            . '<path d="M24 20c2-4 5-7 8-9" stroke="#fff" stroke-opacity=".35" stroke-width="3" fill="none" stroke-linecap="round"/>',
        'ghost' => '<path d="M32 6C19 6 12 16 12 28v28l6-5 6 5 8-6 8 6 6-5 6 5V28C52 16 45 6 32 6z" fill="#FFFDF8" style="stroke:var(--pdsh-ink)" stroke-width="2.5"/>'
            . '<ellipse cx="25" cy="27" rx="3.5" ry="5" style="fill:var(--pdsh-ink)"/>'
            . '<ellipse cx="39" cy="27" rx="3.5" ry="5" style="fill:var(--pdsh-ink)"/>'
            . '<ellipse cx="32" cy="39" rx="4" ry="5" style="fill:var(--pdsh-ink)"/>',
        'tunnel' => '<path d="M6 58V30C6 15 18 6 32 6s26 9 26 24v28z" style="fill:var(--pdsh-ink)"/>'
            . '<path d="M16 58V32c0-9 7-16 16-16s16 7 16 16v26z" style="fill:var(--pdsh-blood)"/>'
            . '<circle cx="27" cy="34" r="2.5" style="fill:var(--pdsh-accent)"/><circle cx="37" cy="34" r="2.5" style="fill:var(--pdsh-accent)"/>',
        'candle' => '<path d="M32 6c5 7 6 11 0 16-6-5-5-9 0-16z" style="fill:var(--pdsh-accent)"/>'
            . '<rect x="24" y="22" width="16" height="36" rx="3" style="fill:var(--pdsh-cream);stroke:var(--pdsh-ink)" stroke-width="2.5"/>'
            . '<path d="M24 30c4 2 4 8 8 8s4-6 8-8" style="stroke:var(--pdsh-blood)" stroke-width="3" fill="none"/>',
        'bat' => '<path d="M32 26c-2-4-3-6-3-8 2 1 3 2 3 3 0-1 1-2 3-3 0 2-1 4-3 8zM32 26c-6-2-14-6-30 0 6 2 8 6 8 10 3-3 6-3 8 0 2-3 5-4 7-2 2 3 4 6 7 8 3-2 5-5 7-8 2-2 5-1 7 2 2-3 5-3 8 0 0-4 2-8 8-10-16-6-24-2-30 0z" fill="currentColor"/>',
    ];

    $body = $paths[$name] ?? $paths['pumpkin'];

    return '<svg class="' . e($class) . '" viewBox="0 0 64 64" aria-hidden="true" focusable="false">' . $body . '</svg>';
}

/** Icona de cada atractiu de la portada, en l'ordre en què s'han escrit. */
function highlight_icon(int $index): string
{
    return spooky_icon(['tunnel', 'chestnut', 'ghost', 'candle', 'pumpkin'][$index % 5]);
}
