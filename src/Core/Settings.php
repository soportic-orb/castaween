<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Configuració editable des del Panell de Gestió (taula `settings`).
 */
final class Settings
{
    private static ?array $cache = null;

    public const DEFAULTS = [
        // Esdeveniment
        'event_name'          => 'Castanyada terrorífica a La Granada',
        'event_tagline'       => 'Gosaràs creuar el túnel?',
        'event_description'   => "Una nit de por i castanyes torrades. Travessa el túnel del terror, escalfa't amb una paperina de castanyes i vine disfressat a celebrar la Castanyada i Halloween a La Granada.",
        'event_date'          => '2026-10-31',
        'event_date_text'     => 'Dissabte 31 d\'octubre de 2026',
        'event_location'      => 'La Granada',
        'event_city'          => '',
        'event_map_url'       => '',
        'event_highlights'    => "Túnel del terror\nCastanyes i panellets\nConcurs de disfresses\nMúsica i contes de por",
        'event_organizer'     => 'AFA Jacint Verdaguer',
        'event_contact_email' => '',
        'event_contact_phone' => '',
        'event_poster'        => '',
        'hero_image'          => '',
        'hero_overlay'        => '55',
        'hero_focus'          => 'center',
        'hero_show_poster'    => '1',
        'event_logo'          => '',

        // Marca / colors (extrets del cartell)
        'brand_primary'   => '#2B1640',
        'brand_secondary' => '#FF6B1A',
        'brand_accent'    => '#FFB81C',
        'brand_cream'     => '#F6ECDD',
        'brand_olive'     => '#6E4A2E',
        'brand_ink'       => '#17111C',

        // Vendes
        'sales_open'          => '1',
        'sales_closed_message' => 'Les inscripcions estan tancades. Gràcies per l\'interès… i compte amb els fantasmes!',
        'currency'            => 'EUR',
        'max_tickets_order'   => '10',
        'terms_url'           => '',
        'privacy_text'        => '',
        'require_terms'       => '1',

        // Anul·lacions
        'cancellation_enabled'       => '1',
        'cancellation_deadline_days' => '7',
        'cancellation_deadline_date' => '',
        'cancellation_fee_percent'   => '0',
        'cancellation_refund'        => '1',
        'cancellation_policy_text'   => 'Pots anul·lar la teva inscripció fins a 7 dies abans de l\'esdeveniment i se\'t retornarà l\'import íntegre.',
        'cancellation_allow_partial' => '1',

        // Stripe
        'stripe_mode'            => 'test',
        'stripe_test_pk'         => '',
        'stripe_test_sk'         => '',
        'stripe_test_wh_secret'  => '',
        'stripe_live_pk'         => '',
        'stripe_live_sk'         => '',
        'stripe_live_wh_secret'  => '',
        'stripe_locale'          => 'auto',
        'stripe_fee_percent'     => '1.5',
        'stripe_fee_fixed_cents' => '25',
        'show_stripe_fee'        => '1',
        'checkout_notice_enabled' => '0',
        'checkout_notice'         => '',

        // SMTP
        'smtp_host'       => '',
        'smtp_port'       => '587',
        'smtp_secure'     => 'tls',
        'smtp_user'       => '',
        'smtp_pass'       => '',
        'smtp_auth'       => '1',
        'smtp_from_email' => '',
        'smtp_from_name'  => 'Castanyada terrorífica',
        'smtp_reply_to'   => '',
        'smtp_batch_size' => '25',
        'mail_last_error'    => '',
        'mail_last_error_at' => '',
        'mail_footer'     => 'Reps aquest correu perquè t\'has inscrit a la Castanyada terrorífica de l\'AFA Jacint Verdaguer.',

        // Correus transaccionals
        'mail_confirmation_subject' => 'La teva inscripció · {{event_name}}',
        'mail_confirmation_body'    => "Hola {{name}},\n\nLa teva inscripció s'ha confirmat correctament.\n\nReferència: {{reference}}\nEntrades: {{ticket_count}}\nImport: {{total}}\n\nTrobaràs les entrades adjuntes en aquest correu en format PDF.\n\nEns veiem al túnel… si t'atreveixes!\n{{event_organizer}}",
        'mail_cancellation_subject' => 'Anul·lació de la inscripció {{reference}}',
        'mail_cancellation_body'    => "Hola {{name}},\n\nHem anul·lat la teva inscripció {{reference}}.\n\n{{refund_note}}\n\nGràcies,\n{{event_organizer}}",

        // Apple / Google Wallet
        'wallet_enabled'            => '0',
        'wallet_expire_hours'       => '48',
        'apple_pass_type_id'        => '',
        'apple_team_id'             => '',
        'apple_organization'        => 'AFA Jacint Verdaguer',
        'apple_cert_path'           => '',
        'apple_key_path'            => '',
        'apple_key_password'        => '',
        'apple_wwdr_path'           => '',
        'google_issuer_id'          => '',
        'google_class_suffix'       => 'castanyada_terrorifica',
        'google_service_account_json' => '',
        'google_class_registered' => '',

        // OTA
        'ota_repo'          => 'soportic-orb/castaween',
        'ota_branch'        => 'main',
        'ota_channel'       => 'branch',
        'ota_token'         => '',
        'ota_auto_check'    => '1',
        'ota_last_check'    => '',
        'ota_latest_version' => '',
        'ota_latest_sha'    => '',
        'ota_latest_ref'    => '',
        'ota_installed_ref' => '',

        // Sistema
        'maintenance_mode'  => '0',
        'cron_token'        => '',
        'last_cron_run'     => '',
        'checkin_enabled'   => '1',
        'timezone'          => 'Europe/Madrid',
        'google_analytics'  => '',
    ];

    public static function all(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }
        $stored = [];
        try {
            foreach (Db::all('SELECT `k`, `v` FROM `settings`') as $row) {
                $stored[$row['k']] = $row['v'];
            }
        } catch (\Throwable) {
            // Encara no instal·lat: fem servir només els valors per defecte.
        }
        self::$cache = array_merge(self::DEFAULTS, $stored);
        return self::$cache;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = self::all();
        $value = $all[$key] ?? self::DEFAULTS[$key] ?? $default;
        return $value === null ? $default : $value;
    }

    public static function bool(string $key, bool $default = false): bool
    {
        $v = self::get($key, $default ? '1' : '0');
        return in_array((string) $v, ['1', 'true', 'on', 'yes'], true);
    }

    public static function int(string $key, int $default = 0): int
    {
        $v = self::get($key, (string) $default);
        return is_numeric($v) ? (int) $v : $default;
    }

    public static function set(string $key, mixed $value): void
    {
        $value = is_scalar($value) || $value === null ? (string) $value : json_encode($value, JSON_UNESCAPED_UNICODE);
        Db::run(
            'INSERT INTO `settings` (`k`, `v`) VALUES (:k, :v) ON DUPLICATE KEY UPDATE `v` = VALUES(`v`)',
            ['k' => $key, 'v' => $value]
        );
        if (self::$cache !== null) {
            self::$cache[$key] = $value;
        }
    }

    public static function setMany(array $pairs): void
    {
        foreach ($pairs as $k => $v) {
            self::set((string) $k, $v);
        }
    }

    public static function flush(): void
    {
        self::$cache = null;
    }

    /** Clau secreta de Stripe segons el mode actiu. */
    public static function stripeSecret(): string
    {
        return (string) self::get(self::get('stripe_mode') === 'live' ? 'stripe_live_sk' : 'stripe_test_sk', '');
    }

    public static function stripePublishable(): string
    {
        return (string) self::get(self::get('stripe_mode') === 'live' ? 'stripe_live_pk' : 'stripe_test_pk', '');
    }

    public static function stripeWebhookSecret(): string
    {
        return (string) self::get(self::get('stripe_mode') === 'live' ? 'stripe_live_wh_secret' : 'stripe_test_wh_secret', '');
    }

    public static function stripeConfigured(): bool
    {
        return self::stripeSecret() !== '' && self::stripePublishable() !== '';
    }
}
