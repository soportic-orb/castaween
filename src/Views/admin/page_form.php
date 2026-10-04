<?php
use App\Core\Csrf;
use App\Services\Pages;

$isNew = (int) ($page['id'] ?? 0) === 0;
$action = $isNew ? url('/admin/pagines/nova') : url('/admin/pagines/' . $page['id']);
$errors = $errors ?? [];
$isLegal = ($page['system_key'] ?? null) !== null;
$checked = static fn (string $field): string => checkedIf((string) old($field, (string) ($page[$field] ?? '0')) === '1');
?>

<p style="display:flex;gap:14px;flex-wrap:wrap;justify-content:space-between;">
    <a href="<?= e(url('/admin/pagines')) ?>">← Tornar a les pàgines</a>
    <?php if (!$isNew): ?>
        <a href="<?= e(Pages::url($page)) ?>" target="_blank" rel="noopener">
            <?= (int) $page['published'] === 1 ? 'Veure la pàgina al web ↗' : 'Previsualitzar l\'esborrany ↗' ?>
        </a>
    <?php endif; ?>
</p>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" class="page-editor">
    <?= Csrf::field() ?>

    <div class="page-editor__main">
        <div class="panel">
            <div class="panel__head"><div><h2>Contingut</h2></div></div>
            <div class="panel__body">
                <div class="field">
                    <label for="title">Títol *</label>
                    <input class="input <?= isset($errors['title']) ? 'has-error' : '' ?>" type="text" id="title" name="title"
                           value="<?= old('title', $page['title']) ?>" required placeholder="El túnel del terror">
                    <?php if (isset($errors['title'])): ?><span class="field__error"><?= e($errors['title']) ?></span><?php endif; ?>
                </div>

                <div class="field">
                    <label for="summary">Resum</label>
                    <input class="input <?= isset($errors['summary']) ? 'has-error' : '' ?>" type="text" id="summary" name="summary" maxlength="300"
                           value="<?= old('summary', $page['summary']) ?>" placeholder="Una frase que surt a la portada i als cercadors.">
                    <?php if (isset($errors['summary'])): ?><span class="field__error"><?= e($errors['summary']) ?></span><?php endif; ?>
                </div>

                <div class="field">
                    <label for="body">Text de la pàgina</label>
                    <textarea class="textarea textarea--code" id="body" name="body" rows="26"
                              placeholder="## Què t'espera&#10;Escriu aquí el text de la pàgina…"><?= old('body', $page['body']) ?></textarea>
                    <span class="field__hint">Fes servir el format que s'explica al requadre «Com donar format al text».</span>
                </div>
            </div>
        </div>
    </div>

    <div class="page-editor__side">
        <div class="panel">
            <div class="panel__head"><div><h2>Publicació</h2></div></div>
            <div class="panel__body">
                <div class="field">
                    <label class="check">
                        <input type="checkbox" name="published" value="1"<?= $checked('published') ?>>
                        <span><strong>Publicada</strong><br><small style="color:var(--pdsh-muted);">Si no ho està, només la veu l'organització.</small></span>
                    </label>
                </div>
                <div class="field">
                    <label class="check"><input type="checkbox" name="show_in_menu" value="1"<?= $checked('show_in_menu') ?>><span>Mostrar al menú de dalt</span></label>
                </div>
                <div class="field">
                    <label class="check"><input type="checkbox" name="show_on_home" value="1"<?= $checked('show_on_home') ?>><span>Mostrar a la portada</span></label>
                </div>
                <div class="field">
                    <label class="check"><input type="checkbox" name="show_in_footer" value="1"<?= $checked('show_in_footer') ?>><span>Mostrar al peu de pàgina</span></label>
                </div>

                <div class="form-grid">
                    <div class="field">
                        <label for="slug">Adreça</label>
                        <input class="input <?= isset($errors['slug']) ? 'has-error' : '' ?>" type="text" id="slug" name="slug"
                               value="<?= old('slug', $page['slug']) ?>" placeholder="es crea a partir del títol">
                        <span class="field__hint">/pagina/<?= e($page['slug'] !== '' ? $page['slug'] : '…') ?></span>
                        <?php if (isset($errors['slug'])): ?><span class="field__error"><?= e($errors['slug']) ?></span><?php endif; ?>
                    </div>
                    <div class="field">
                        <label for="sort_order">Ordre</label>
                        <input class="input" type="number" id="sort_order" name="sort_order" value="<?= old('sort_order', $page['sort_order']) ?>">
                        <span class="field__hint">Les de número més baix surten primer.</span>
                    </div>
                </div>

                <div class="field">
                    <label for="cover">Imatge de capçalera</label>
                    <?php if (!empty($page['cover_image'])): ?>
                        <img src="<?= e(url((string) $page['cover_image'])) ?>" alt="" style="max-height:120px;border-radius:8px;margin-bottom:8px;">
                        <label class="check" style="margin-bottom:8px;"><input type="checkbox" name="remove_cover" value="1"><span>Treure la imatge</span></label>
                    <?php endif; ?>
                    <input class="input" type="file" id="cover" name="cover" accept="image/jpeg,image/png,image/webp">
                    <span class="field__hint">JPG, PNG o WEBP, màxim 6 MB.</span>
                </div>

                <button type="submit" class="btn btn--primary btn--block" data-loading="Desant…">
                    <?= $isNew ? 'Crear la pàgina' : 'Desar els canvis' ?>
                </button>
                <?php if ($isLegal): ?>
                    <p class="field__hint" style="margin-top:12px;">És una pàgina legal: es pot editar i despublicar, però no eliminar. El web l'enllaça des del peu i del formulari d'inscripció.</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head"><div><h2>Com donar format al text</h2></div></div>
            <div class="panel__body format-help">
                <table>
                    <tr><td><code>## Títol</code></td><td>Títol de secció</td></tr>
                    <tr><td><code>### Subtítol</code></td><td>Subtítol</td></tr>
                    <tr><td><code>**negreta**</code></td><td><strong>negreta</strong></td></tr>
                    <tr><td><code>*cursiva*</code></td><td><em>cursiva</em></td></tr>
                    <tr><td><code>- element</code></td><td>Llista de punts</td></tr>
                    <tr><td><code>1. element</code></td><td>Llista numerada</td></tr>
                    <tr><td><code>[text](https://…)</code></td><td>Enllaç</td></tr>
                    <tr><td><code>![descripció](/uploads/…)</code></td><td>Imatge</td></tr>
                    <tr><td><code>&gt; text</code></td><td>Requadre destacat</td></tr>
                    <tr><td><code>| A | B |</code><br><code>| --- | --- |</code></td><td>Taula</td></tr>
                    <tr><td><code>---</code></td><td>Línia separadora</td></tr>
                </table>
                <p class="field__hint">Deixa una línia en blanc entre paràgrafs.</p>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head"><div><h2>Dades automàtiques</h2></div></div>
            <div class="panel__body format-help">
                <p class="field__hint" style="margin-top:0;">Escriu aquests marcadors i el web hi posarà el valor de la configuració:</p>
                <table>
                    <?php foreach ($variables as $key => [$value, $isHtml, $label]): ?>
                        <tr>
                            <td><code>{{<?= e($key) ?>}}</code></td>
                            <td><?= $isHtml ? e($label) : ($value === '' ? '<span style="color:var(--pdsh-danger);">Pendent</span>' : e(\App\Core\Str::limit($value, 40))) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</form>
