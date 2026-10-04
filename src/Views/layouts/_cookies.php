<?php
/**
 * Bàner de galetes (RGPD i LSSI-CE, art. 22.2).
 *
 * Les galetes tècniques no necessiten consentiment. Les d'analítica (Google
 * Analytics) només es carreguen si la persona les accepta, i es pot canviar
 * la tria en qualsevol moment des del peu («Configurar galetes»). Acceptar i
 * rebutjar tenen el mateix pes visual, tal com demana la guia de l'AEPD.
 */
use App\Services\Pages;

$analyticsId = Pages::analyticsId();
$hasOptional = $analyticsId !== '';
?>
<div class="cookie-banner" id="cookie-banner" role="dialog" aria-modal="false" aria-labelledby="cookie-title" hidden
     data-cookie-version="<?= $hasOptional ? '2' : '1' ?>" data-cookie-optional="<?= $hasOptional ? '1' : '0' ?>"
     <?= $hasOptional ? 'data-ga-id="' . e($analyticsId) . '"' : '' ?>>
    <div class="cookie-banner__inner">
        <div class="cookie-banner__icon"><?= spooky_icon('chestnut') ?></div>
        <div class="cookie-banner__text">
            <h2 id="cookie-title">Galetes en aquest web</h2>
            <?php if ($hasOptional): ?>
                <p>Fem servir galetes pròpies imprescindibles perquè el web funcioni, per exemple per mantenir la
                   inscripció mentre la fas. Amb el teu permís, també farem servir galetes d'analítica de Google
                   Analytics per saber com es fa servir el web. Pots acceptar-les, rebutjar-les o triar-les.</p>
            <?php else: ?>
                <p>Aquest web només fa servir galetes tècniques imprescindibles perquè funcioni, per exemple per
                   mantenir la inscripció mentre la fas. No fem servir galetes de publicitat ni d'analítica.</p>
            <?php endif; ?>
            <p class="cookie-banner__more">
                <a href="<?= e(Pages::urlFor(Pages::COOKIES)) ?>">Política de galetes</a> ·
                <a href="<?= e(Pages::urlFor(Pages::PRIVACY)) ?>">Política de privacitat</a>
            </p>

            <?php if ($hasOptional): ?>
                <div class="cookie-banner__prefs" id="cookie-prefs" hidden>
                    <label class="cookie-option">
                        <input type="checkbox" checked disabled>
                        <span><strong>Tècniques</strong> · sempre actives<br>
                              <small>Sessió de la inscripció, seguretat dels formularis i aquesta tria.</small></span>
                    </label>
                    <label class="cookie-option">
                        <input type="checkbox" id="cookie-analytics">
                        <span><strong>Analítica</strong> · Google Analytics<br>
                              <small>Estadístiques anònimes de visites. Només si les acceptes.</small></span>
                    </label>
                </div>
            <?php endif; ?>
        </div>

        <div class="cookie-banner__actions">
            <?php if ($hasOptional): ?>
                <button type="button" class="btn btn--accent btn--sm" data-cookie-accept>Acceptar-les totes</button>
                <button type="button" class="btn btn--accent btn--sm" data-cookie-reject>Rebutjar les opcionals</button>
                <button type="button" class="btn btn--ghost-light btn--sm" data-cookie-configure>Configurar</button>
                <button type="button" class="btn btn--accent btn--sm" data-cookie-save hidden>Desar la tria</button>
            <?php else: ?>
                <button type="button" class="btn btn--accent btn--sm" data-cookie-reject>Entesos</button>
            <?php endif; ?>
        </div>
    </div>
</div>
<script src="<?= e(asset('js/cookies.js')) ?>" defer></script>
