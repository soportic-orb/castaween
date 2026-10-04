<?php
use App\Core\Settings;
use App\Core\View;
?>
<!DOCTYPE html>
<html lang="ca">
<head>
<?= View::partial('layouts/_head', get_defined_vars()) ?>
</head>
<body>

<header class="site-header">
    <div class="wrap site-header__inner">
        <a class="site-header__brand" href="<?= e(url('/')) ?>">
            <?php if (($logo = (string) Settings::get('event_logo')) !== ''): ?>
                <img class="site-header__logo" src="<?= e(url($logo)) ?>" alt="">
            <?php else: ?>
                <span class="site-header__mark"><?= spooky_icon('pumpkin') ?></span>
            <?php endif; ?>
            <span class="site-header__title">
                <?= e(Settings::get('event_name')) ?>
                <small><?= e(Settings::get('event_date_text')) ?></small>
            </span>
        </a>

        <nav class="site-nav" aria-label="Navegació principal">
            <?php foreach (\App\Services\Pages::menu() as $menuPage): ?>
                <a data-nav="page" href="<?= e(\App\Services\Pages::url($menuPage)) ?>"><?= e($menuPage['title']) ?></a>
            <?php endforeach; ?>
            <?php if (\App\Services\Pages::menu() === []): ?>
                <a data-nav="info" href="<?= e(url('/')) ?>#informacio">Informació</a>
            <?php endif; ?>
            <a href="<?= e(url('/les-meves-entrades')) ?>">Les meves entrades</a>
            <?php if (\App\Services\TicketService::salesOpen()): ?>
                <a class="is-cta" href="<?= e(url('/')) ?>#inscripcions">Inscriure's</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<div class="bunting" aria-hidden="true"></div>

<main>
    <?php if (!empty($showFlashInWrap ?? true)): ?>
        <?php $flash = View::partial('layouts/_flash'); ?>
        <?php if (trim($flash) !== ''): ?>
            <div class="wrap" style="padding-top:20px;"><?= $flash ?></div>
        <?php endif; ?>
    <?php endif; ?>

    <?= $content ?? '' ?>
</main>

<footer class="site-footer">
    <div class="wrap">
        <div class="site-footer__grid">
            <div>
                <h4><?= e(Settings::get('event_name')) ?></h4>
                <p><?= e(Settings::get('event_date_text')) ?><br>
                   <?= e(trim((string) Settings::get('event_location') . ' ' . (string) Settings::get('event_city'))) ?></p>
            </div>
            <div>
                <h4>Contacte</h4>
                <p>
                    <?php if (($email = (string) Settings::get('event_contact_email')) !== ''): ?>
                        <a href="mailto:<?= e($email) ?>"><?= e($email) ?></a><br>
                    <?php endif; ?>
                    <?php if (($phone = (string) Settings::get('event_contact_phone')) !== ''): ?>
                        <a href="tel:<?= e(preg_replace('/\s+/', '', $phone)) ?>"><?= e($phone) ?></a>
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <h4>Entrades</h4>
                <p>
                    <a href="<?= e(url('/les-meves-entrades')) ?>">Consultar o anul·lar la inscripció</a><br>
                    <a href="<?= e(url('/informacio')) ?>">Informació i anul·lacions</a>
                    <?php foreach (\App\Services\Pages::footer() as $footerPage): if ($footerPage['system_key'] !== null) { continue; } ?>
                        <br><a href="<?= e(\App\Services\Pages::url($footerPage)) ?>"><?= e($footerPage['title']) ?></a>
                    <?php endforeach; ?>
                </p>
            </div>
        </div>
        <nav class="site-footer__legal" aria-label="Informació legal">
            <?php foreach (\App\Services\Pages::footer() as $footerPage): if ($footerPage['system_key'] === null) { continue; } ?>
                <a href="<?= e(\App\Services\Pages::url($footerPage)) ?>"><?= e($footerPage['title']) ?></a>
            <?php endforeach; ?>
            <button type="button" class="link-button" data-cookie-settings>Configurar galetes</button>
        </nav>
        <div class="site-footer__bottom">
            <span>© <?= date('Y') ?> · <?= e(Settings::get('event_organizer')) ?></span>
            <span>Pagaments segurs amb Stripe · <?= e(\App\Core\Url::host()) ?></span>
        </div>
    </div>
</footer>

<script src="<?= e(asset('js/app.js')) ?>" defer></script>
<?php if (!empty($useConfetti)): ?>
    <script src="<?= e(asset('js/confetti.js')) ?>" defer></script>
<?php endif; ?>
<?= \App\Core\View::partial('layouts/_cookies') ?>
</body>
</html>
