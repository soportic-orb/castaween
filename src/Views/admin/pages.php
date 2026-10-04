<?php
use App\Core\Csrf;
use App\Services\Pages;
?>

<?php if ($missing !== []): ?>
    <div class="alert alert--warning">
        <span aria-hidden="true">⚠️</span>
        <span>
            Als textos legals hi falten dades: <strong><?= e(implode(', ', $missing)) ?></strong>.
            Mentre no les omplis, el web públic hi mostra un avís «pendent».
            <a href="<?= e(url('/admin/configuracio')) ?>#dades-legals">Completa-les a Configuració → Esdeveniment</a>.
        </span>
    </div>
<?php endif; ?>

<div class="panel">
    <div class="panel__head">
        <div>
            <h2>Pàgines</h2>
            <p>Pàgines d'informació del web: el túnel del terror, horaris, com arribar… i els textos legals.</p>
        </div>
        <a class="btn btn--primary btn--sm" href="<?= e(url('/admin/pagines/nova')) ?>">+ Nova pàgina</a>
    </div>

    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr><th>Títol</th><th>Adreça</th><th>On surt</th><th>Estat</th><th>Actualitzada</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($pages as $page):
                    $places = array_filter([
                        (int) $page['show_in_menu'] === 1 ? 'Menú' : null,
                        (int) $page['show_on_home'] === 1 ? 'Portada' : null,
                        (int) $page['show_in_footer'] === 1 ? 'Peu' : null,
                    ]);
                ?>
                    <tr>
                        <td>
                            <a href="<?= e(url('/admin/pagines/' . $page['id'])) ?>"><strong><?= e($page['title']) ?></strong></a>
                            <?php if ($page['system_key'] !== null): ?>
                                <span class="badge badge--muted" title="Pàgina legal: es pot editar però no eliminar">Legal</span>
                            <?php endif; ?>
                            <?php if (trim((string) $page['summary']) !== ''): ?>
                                <br><span style="color:var(--pdsh-muted);font-size:.83rem;"><?= e(\App\Core\Str::limit((string) $page['summary'], 80)) ?></span>
                            <?php endif; ?>
                        </td>
                        <td style="font-size:.85rem;">
                            <a href="<?= e(Pages::url($page)) ?>" target="_blank" rel="noopener">/pagina/<?= e($page['slug']) ?> ↗</a>
                        </td>
                        <td style="font-size:.85rem;color:var(--pdsh-muted);"><?= $places === [] ? '—' : e(implode(' · ', $places)) ?></td>
                        <td>
                            <span class="badge <?= (int) $page['published'] === 1 ? 'badge--ok' : 'badge--warn' ?>">
                                <?= (int) $page['published'] === 1 ? 'Publicada' : 'Esborrany' ?>
                            </span>
                        </td>
                        <td style="font-size:.85rem;color:var(--pdsh-muted);"><?= e(dt((string) $page['updated_at'], 'd/m/Y')) ?></td>
                        <td>
                            <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap;">
                                <form method="post" action="<?= e(url('/admin/pagines/' . $page['id'] . '/estat')) ?>">
                                    <?= Csrf::field() ?>
                                    <button type="submit" class="btn btn--light btn--sm">
                                        <?= (int) $page['published'] === 1 ? 'Despublicar' : 'Publicar' ?>
                                    </button>
                                </form>
                                <?php if ($page['system_key'] === null): ?>
                                    <form method="post" action="<?= e(url('/admin/pagines/' . $page['id'] . '/eliminar')) ?>"
                                          data-confirm="Eliminar la pàgina «<?= e($page['title']) ?>»? No es pot desfer.">
                                        <?= Csrf::field() ?>
                                        <button type="submit" class="btn btn--danger btn--sm">Eliminar</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if ($pages === []): ?>
                    <tr><td colspan="6" class="empty">
                        <span class="empty__icon" aria-hidden="true">📄</span>
                        Encara no hi ha cap pàgina.<br>
                        <a class="btn btn--primary btn--sm" style="margin-top:14px;" href="<?= e(url('/admin/pagines/nova')) ?>">Crear-ne una</a>
                    </td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
