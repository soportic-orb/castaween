<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Format de text senzill per a les pàgines del CMS, inspirat en Markdown.
 *
 * Tot el text s'escapa abans de donar-li format, de manera que des del panell
 * no es pot injectar HTML. Admet:
 *
 *   ## Títol / ### Subtítol      títols de secció
 *   **negreta**, *cursiva*        format en línia
 *   [text](https://...)           enllaços (també /ruta, mailto: i tel:)
 *   ![text alternatiu](/uploads/imatge.jpg)   imatge en una línia sola
 *   - element / 1. element        llistes
 *   > text                        requadre destacat
 *   | A | B |                     taules (la segona línia, | --- | --- |)
 *   ---                           línia separadora
 *
 * Els marcadors {{clau}} es deixen tal qual perquè els substitueixi qui crida.
 */
final class Markdown
{
    public static function render(string $text): string
    {
        $lines = preg_split('/\R/', str_replace("\t", '    ', $text)) ?: [];
        $html = [];
        $paragraph = [];
        $list = null;        // ['ul'|'ol', items[]]
        $quote = [];
        $count = count($lines);

        $flushParagraph = static function () use (&$paragraph, &$html): void {
            if ($paragraph !== []) {
                $html[] = '<p>' . implode('<br>', array_map([self::class, 'inline'], $paragraph)) . '</p>';
                $paragraph = [];
            }
        };
        $flushList = static function () use (&$list, &$html): void {
            if ($list !== null) {
                $items = array_map(static fn (string $i) => '<li>' . self::inline($i) . '</li>', $list[1]);
                $html[] = '<' . $list[0] . '>' . implode('', $items) . '</' . $list[0] . '>';
                $list = null;
            }
        };
        $flushQuote = static function () use (&$quote, &$html): void {
            if ($quote !== []) {
                $html[] = '<blockquote>' . self::render(implode("\n", $quote)) . '</blockquote>';
                $quote = [];
            }
        };
        $flushAll = static function () use ($flushParagraph, $flushList, $flushQuote): void {
            $flushParagraph();
            $flushList();
            $flushQuote();
        };

        for ($i = 0; $i < $count; $i++) {
            $line = rtrim($lines[$i]);
            $trimmed = ltrim($line);

            if ($trimmed === '') {
                $flushAll();
                continue;
            }

            if (preg_match('/^>\s?(.*)$/', $trimmed, $m)) {
                $flushParagraph();
                $flushList();
                $quote[] = $m[1];
                continue;
            }
            $flushQuote();

            if (preg_match('/^(#{1,4})\s+(.+?)\s*#*$/', $trimmed, $m)) {
                $flushAll();
                $level = max(2, strlen($m[1]));
                $html[] = sprintf('<h%1$d id="%2$s">%3$s</h%1$d>', $level, e(Str::slug($m[2])), self::inline($m[2]));
                continue;
            }

            if (preg_match('/^(-{3,}|\*{3,}|_{3,})$/', $trimmed)) {
                $flushAll();
                $html[] = '<hr>';
                continue;
            }

            if (preg_match('/^!\[([^\]]*)\]\(([^)\s]+)\)$/', $trimmed, $m)) {
                $flushAll();
                $src = self::safeUrl($m[2]);
                $alt = e($m[1]);
                $html[] = '<figure><img src="' . e($src) . '" alt="' . $alt . '" loading="lazy">'
                    . ($m[1] !== '' ? '<figcaption>' . $alt . '</figcaption>' : '') . '</figure>';
                continue;
            }

            // Taula: una fila amb | seguida d'una fila separadora | --- |.
            if (str_starts_with($trimmed, '|') && isset($lines[$i + 1])
                && preg_match('/^\|?\s*:?-{3,}:?\s*(\|\s*:?-{3,}:?\s*)*\|?$/', trim($lines[$i + 1]))) {
                $flushAll();
                $head = self::cells($trimmed);
                $rows = [];
                $i += 2;
                while ($i < $count && str_starts_with(trim($lines[$i]), '|')) {
                    $rows[] = self::cells(trim($lines[$i]));
                    $i++;
                }
                $i--;
                $table = '<div class="prose-table"><table><thead><tr>';
                foreach ($head as $cell) {
                    $table .= '<th>' . self::inline($cell) . '</th>';
                }
                $table .= '</tr></thead><tbody>';
                foreach ($rows as $row) {
                    $table .= '<tr>';
                    foreach ($head as $index => $unused) {
                        $table .= '<td>' . self::inline($row[$index] ?? '') . '</td>';
                    }
                    $table .= '</tr>';
                }
                $html[] = $table . '</tbody></table></div>';
                continue;
            }

            if (preg_match('/^[-*]\s+(.*)$/', $trimmed, $m) || preg_match('/^\d+[.)]\s+(.*)$/', $trimmed, $m)) {
                $flushParagraph();
                $type = preg_match('/^\d/', $trimmed) ? 'ol' : 'ul';
                if ($list !== null && $list[0] !== $type) {
                    $flushList();
                }
                $list ??= [$type, []];
                $list[1][] = $m[1];
                continue;
            }

            // Línia que continua l'element anterior d'una llista (sagnada).
            if ($list !== null && $line !== $trimmed) {
                $list[1][count($list[1]) - 1] .= ' ' . $trimmed;
                continue;
            }

            $flushList();
            $paragraph[] = $trimmed;
        }

        $flushAll();

        return implode("\n", $html);
    }

    /** Format en línia sobre text ja escapat. */
    public static function inline(string $text): string
    {
        $text = e($text);

        // Imatges dins del text i enllaços.
        $text = preg_replace_callback(
            '/!\[([^\]]*)\]\(([^)\s]+)\)/',
            static fn (array $m) => '<img src="' . e(self::safeUrl(html_entity_decode($m[2], ENT_QUOTES))) . '" alt="' . $m[1] . '" loading="lazy">',
            $text
        ) ?? $text;

        $text = preg_replace_callback(
            '/\[([^\]]+)\]\(([^)\s]+)\)/',
            static function (array $m): string {
                $url = self::safeUrl(html_entity_decode($m[2], ENT_QUOTES));
                $external = preg_match('#^https?://#i', $url) === 1 && !str_starts_with($url, Url::base());
                return '<a href="' . e($url) . '"' . ($external ? ' target="_blank" rel="noopener"' : '') . '>' . $m[1] . '</a>';
            },
            $text
        ) ?? $text;

        // Adreces web soltes que no formen part d'un enllaç.
        $text = preg_replace(
            '#(?<!["=>/])\b(https?://[^\s<]+[^\s<.,;:!?)])#i',
            '<a href="$1" target="_blank" rel="noopener">$1</a>',
            $text
        ) ?? $text;

        $text = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $text) ?? $text;
        $text = preg_replace('/(?<![*\w])\*(?!\s)(.+?)(?<!\s)\*(?![*\w])/s', '<em>$1</em>', $text) ?? $text;
        $text = preg_replace('/`([^`]+)`/', '<code>$1</code>', $text) ?? $text;

        return $text;
    }

    /** Només deixa passar adreces web, rutes internes, correu i telèfon. */
    public static function safeUrl(string $url): string
    {
        $url = trim($url);
        if (str_starts_with($url, '/') && !str_starts_with($url, '//')) {
            return url($url);
        }
        if (preg_match('#^(https?://|mailto:|tel:|\#)#i', $url) === 1) {
            return $url;
        }
        return '#';
    }

    /** @return string[] */
    private static function cells(string $row): array
    {
        $row = trim($row);
        $row = preg_replace('/^\||\|$/', '', $row) ?? $row;
        return array_map('trim', explode('|', $row));
    }
}
