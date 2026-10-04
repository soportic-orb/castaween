<?php
/** Pàgina de contingut del CMS. */
$cover = trim((string) ($page['cover_image'] ?? ''));
?>

<?php if ($isDraft): ?>
    <div class="draft-bar">
        <div class="wrap">
            <strong>Esborrany:</strong> aquesta pàgina no està publicada i només la veu l'organització.
            <a href="<?= e(url('/admin/pagines/' . $page['id'])) ?>">Editar-la</a>
        </div>
    </div>
<?php endif; ?>

<div class="page-head <?= $cover !== '' ? 'page-head--cover' : '' ?>"
     <?= $cover !== '' ? 'style="--cover:url(\'' . e(url($cover)) . '\')"' : '' ?>>
    <div class="wrap wrap--narrow">
        <a class="page-head__back" href="<?= e(url('/')) ?>">← Tornar a l'inici</a>
        <h1><?= e($page['title']) ?></h1>
        <?php if (trim((string) $page['summary']) !== ''): ?>
            <p><?= e($page['summary']) ?></p>
        <?php endif; ?>
    </div>
</div>

<section class="section">
    <div class="wrap wrap--narrow">
        <article class="card"><div class="card__body prose">
            <?= $bodyHtml ?>
        </div></article>

        <?php if (\App\Services\TicketService::salesOpen() && $page['system_key'] === null): ?>
            <p style="text-align:center;margin-top:28px;">
                <a class="btn btn--accent btn--lg" href="<?= e(url('/')) ?>#inscripcions">Vull la meva entrada →</a>
            </p>
        <?php endif; ?>
    </div>
</section>
