<?php
/** Fragment comú: metadades, variables de color i fulls d'estil. */
use App\Core\Settings;
use App\Core\Url;

$pageTitle = ($title ?? '') !== ''
    ? $title . ' · ' . Settings::get('event_name')
    : (string) Settings::get('event_name');
?>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($metaDescription ?? Settings::get('event_description')) ?>">
<meta name="theme-color" content="<?= e(Settings::get('brand_primary')) ?>">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($metaDescription ?? Settings::get('event_description')) ?>">
<meta property="og:type" content="website">
<meta property="og:url" content="<?= e(Url::base() . ($_SERVER['REQUEST_URI'] ?? '/')) ?>">
<?php if (($poster = (string) Settings::get('event_poster')) !== ''): ?>
    <meta property="og:image" content="<?= e(Url::base() . $poster) ?>">
<?php endif; ?>
<?php
// Icona de la pestanya: una carabassa amb els colors configurats.
$favicon = sprintf(
    '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64">'
    . '<rect width="64" height="64" rx="14" fill="%1$s"/>'
    . '<path d="M32 16c-2-5 1-9 5-10-2 3-3 6-2 10z" fill="%4$s"/>'
    . '<ellipse cx="22" cy="38" rx="12" ry="17" fill="%2$s"/><ellipse cx="42" cy="38" rx="12" ry="17" fill="%2$s"/>'
    . '<ellipse cx="32" cy="38" rx="11" ry="18" fill="%2$s"/>'
    . '<path d="M22 34l5-5 3 5zM34 34l5-5 3 5z" fill="%3$s"/>'
    . '<path d="M21 43c4 5 18 5 22 0l-3 2-3-3-3 3-2-3-3 3-3-3-3 3z" fill="%3$s"/>'
    . '</svg>',
    Settings::get('brand_primary'),
    Settings::get('brand_secondary'),
    Settings::get('brand_ink'),
    Settings::get('brand_olive')
);
?>
<link rel="icon" href="data:image/svg+xml,<?= rawurlencode($favicon) ?>">
<link rel="preload" as="font" type="font/woff2" href="<?= e(url('/assets/fonts/creepster-latin.woff2')) ?>" crossorigin>
<link rel="preload" as="font" type="font/woff2" href="<?= e(url('/assets/fonts/nunito-400-latin.woff2')) ?>" crossorigin>
<link rel="stylesheet" href="<?= e(asset('css/fonts.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/app.css')) ?>">
<style>
    :root {
        --pdsh-primary: <?= e(Settings::get('brand_primary')) ?>;
        --pdsh-secondary: <?= e(Settings::get('brand_secondary')) ?>;
        --pdsh-accent: <?= e(Settings::get('brand_accent')) ?>;
        --pdsh-cream: <?= e(Settings::get('brand_cream')) ?>;
        --pdsh-olive: <?= e(Settings::get('brand_olive')) ?>;
        --pdsh-ink: <?= e(Settings::get('brand_ink')) ?>;
<?php
// Tira de teranyines sota la capçalera, dibuixada amb els colors configurats.
$bunting = sprintf(
    '<svg xmlns="http://www.w3.org/2000/svg" width="96" height="26" viewBox="0 0 96 26">'
    . '<path d="M0 0h96v3H0z" fill="%1$s"/>'
    . '<g fill="none" stroke="%2$s" stroke-opacity=".55" stroke-width="1.2">'
    . '<path d="M48 0v22M48 0L30 16M48 0L66 16M48 0L38 21M48 0L58 21"/>'
    . '<path d="M41 6q7 4 14 0M36 11q12 6 24 0M32 16q16 8 32 0"/>'
    . '</g>'
    . '<path d="M16 0v13" stroke="%2$s" stroke-opacity=".5" stroke-width="1"/>'
    . '<ellipse cx="16" cy="16" rx="3.2" ry="3.8" fill="%1$s"/>'
    . '<path d="M12 13l-4-3M12 16H7M12 19l-4 3M20 13l4-3M20 16h5M20 19l4 3" stroke="%1$s" stroke-width="1.2"/>'
    . '</svg>',
    Settings::get('brand_ink'),
    Settings::get('brand_cream')
);
?>
        --pdsh-bunting: url("data:image/svg+xml,<?= rawurlencode($bunting) ?>");
    }
</style>
