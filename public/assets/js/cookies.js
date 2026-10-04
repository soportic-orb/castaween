/* Bàner de galetes. Desa la tria a la galeta «ct_galetes» durant 6 mesos i
   només carrega Google Analytics si la persona ha acceptat l'analítica. */
(function () {
    'use strict';

    var NAME = 'ct_galetes';
    var MAX_AGE = 60 * 60 * 24 * 182;

    function readConsent(version) {
        var match = document.cookie.match(new RegExp('(?:^|; )' + NAME + '=([^;]*)'));
        if (!match) { return null; }
        var parts = decodeURIComponent(match[1]).split('|');
        if (parts[0] !== 'v' + version) { return null; }
        return { analytics: parts[1] === 'a1' };
    }

    function writeConsent(version, analytics) {
        var secure = location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = NAME + '=' + encodeURIComponent('v' + version + '|' + (analytics ? 'a1' : 'a0') + '|' + Date.now())
            + '; Max-Age=' + MAX_AGE + '; Path=/; SameSite=Lax' + secure;
    }

    /* En retirar el consentiment, s'esborren les galetes de Google Analytics. */
    function clearAnalyticsCookies() {
        var host = location.hostname;
        var domains = ['', host, '.' + host, '.' + host.split('.').slice(-2).join('.')];
        document.cookie.split('; ').forEach(function (pair) {
            var name = pair.split('=')[0];
            if (name === '_ga' || name.indexOf('_ga_') === 0 || name === '_gid') {
                domains.forEach(function (domain) {
                    document.cookie = name + '=; Max-Age=0; Path=/' + (domain ? '; Domain=' + domain : '');
                });
            }
        });
    }

    function loadAnalytics(id) {
        if (!id || window.ctAnalyticsLoaded) { return; }
        window.ctAnalyticsLoaded = true;
        var script = document.createElement('script');
        script.async = true;
        script.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(id);
        document.head.appendChild(script);
        window.dataLayer = window.dataLayer || [];
        window.gtag = function () { window.dataLayer.push(arguments); };
        window.gtag('js', new Date());
        window.gtag('config', id, { anonymize_ip: true });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var banner = document.getElementById('cookie-banner');
        if (!banner) { return; }

        var version = banner.getAttribute('data-cookie-version') || '1';
        var gaId = banner.getAttribute('data-ga-id');
        var prefs = document.getElementById('cookie-prefs');
        var analyticsBox = document.getElementById('cookie-analytics');
        var saveButton = banner.querySelector('[data-cookie-save]');
        var configureButton = banner.querySelector('[data-cookie-configure]');
        var consent = readConsent(version);

        function open(showPrefs) {
            banner.hidden = false;
            document.body.classList.add('has-cookie-banner');
            if (prefs) {
                prefs.hidden = !showPrefs;
                if (analyticsBox) { analyticsBox.checked = !!(consent && consent.analytics); }
                if (saveButton) { saveButton.hidden = !showPrefs; }
                if (configureButton) { configureButton.hidden = !!showPrefs; }
            }
            var first = banner.querySelector('button:not([hidden])');
            if (first && showPrefs !== undefined) { first.focus(); }
        }

        function close() {
            banner.hidden = true;
            document.body.classList.remove('has-cookie-banner');
        }

        function decide(analytics) {
            var hadAnalytics = !!(consent && consent.analytics);
            writeConsent(version, analytics);
            consent = { analytics: analytics };
            close();
            if (analytics) {
                loadAnalytics(gaId);
            } else {
                clearAnalyticsCookies();
                if (hadAnalytics && window.ctAnalyticsLoaded) { location.reload(); }
            }
        }

        if (consent === null) {
            open(false);
        } else if (consent.analytics) {
            loadAnalytics(gaId);
        }

        banner.addEventListener('click', function (event) {
            var target = event.target.closest('button');
            if (!target) { return; }
            if (target.hasAttribute('data-cookie-accept')) { decide(true); }
            if (target.hasAttribute('data-cookie-reject')) { decide(false); }
            if (target.hasAttribute('data-cookie-configure')) { open(true); }
            if (target.hasAttribute('data-cookie-save')) { decide(!!(analyticsBox && analyticsBox.checked)); }
        });

        document.querySelectorAll('[data-cookie-settings]').forEach(function (button) {
            button.addEventListener('click', function () { open(!!prefs); });
        });
    });
})();
