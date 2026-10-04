/* Celebració de la pantalla de compra realitzada: castanyes, fulles de tardor
   i algun ratpenat que cauen. Sense dependències i respectant la preferència
   de moviment reduït. Els colors surten de les variables del Panell. */
(function () {
    'use strict';

    function launch(options) {
        var settings = options || {};
        var duration = settings.duration || 4200;
        var css = window.getComputedStyle(document.documentElement);
        function token(name, fallback) {
            var value = css.getPropertyValue(name).trim();
            return value || fallback;
        }
        var chestnut = token('--pdsh-olive', '#6E4A2E');
        var shell = token('--pdsh-cream', '#F6ECDD');
        var ink = token('--pdsh-ink', '#17111C');
        var colors = settings.colors || [
            token('--pdsh-secondary', '#FF6B1A'),
            token('--pdsh-accent', '#FFB81C'),
            token('--pdsh-blood', '#B3122E'),
            chestnut
        ];

        if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            return;
        }

        var canvas = document.getElementById('confetti-canvas');
        if (!canvas) {
            canvas = document.createElement('canvas');
            canvas.id = 'confetti-canvas';
            document.body.appendChild(canvas);
        }

        var context = canvas.getContext('2d');
        var ratio = window.devicePixelRatio || 1;
        var pieces = [];
        var running = true;
        var started = Date.now();

        function resize() {
            canvas.width = window.innerWidth * ratio;
            canvas.height = window.innerHeight * ratio;
            canvas.style.width = window.innerWidth + 'px';
            canvas.style.height = window.innerHeight + 'px';
            context.setTransform(ratio, 0, 0, ratio, 0, 0);
        }

        function createPiece(fromTop) {
            return {
                x: Math.random() * window.innerWidth,
                y: fromTop ? -20 - Math.random() * window.innerHeight * 0.4 : Math.random() * window.innerHeight,
                kind: Math.random() < 0.38 ? 'chestnut' : (Math.random() < 0.9 ? 'leaf' : 'bat'),
                size: 9 + Math.random() * 8,
                color: colors[Math.floor(Math.random() * colors.length)],
                rotation: Math.random() * Math.PI * 2,
                spin: (Math.random() - 0.5) * 0.24,
                speed: 1.9 + Math.random() * 2.6,
                drift: (Math.random() - 0.5) * 1.5,
                wobble: Math.random() * Math.PI * 2
            };
        }

        function draw(p) {
            var s = p.size;
            if (p.kind === 'chestnut') {
                context.fillStyle = chestnut;
                context.beginPath();
                context.moveTo(0, -s * 0.75);
                context.bezierCurveTo(s * 0.9, -s * 0.1, s * 0.75, s * 0.6, 0, s * 0.6);
                context.bezierCurveTo(-s * 0.75, s * 0.6, -s * 0.9, -s * 0.1, 0, -s * 0.75);
                context.fill();
                context.fillStyle = shell;
                context.globalAlpha = 0.75;
                context.beginPath();
                context.ellipse(0, s * 0.42, s * 0.55, s * 0.2, 0, 0, Math.PI * 2);
                context.fill();
                context.globalAlpha = 1;
            } else if (p.kind === 'bat') {
                context.fillStyle = ink;
                context.scale(s / 16, s / 16);
                context.beginPath();
                context.moveTo(0, -2);
                context.bezierCurveTo(-5, -6, -12, -6, -18, -2);
                context.bezierCurveTo(-14, 0, -13, 3, -13, 5);
                context.bezierCurveTo(-10, 2, -7, 3, -6, 5);
                context.bezierCurveTo(-4, 3, -2, 4, 0, 6);
                context.bezierCurveTo(2, 4, 4, 3, 6, 5);
                context.bezierCurveTo(7, 3, 10, 2, 13, 5);
                context.bezierCurveTo(13, 3, 14, 0, 18, -2);
                context.bezierCurveTo(12, -6, 5, -6, 0, -2);
                context.fill();
            } else {
                context.fillStyle = p.color;
                context.beginPath();
                context.moveTo(0, -s * 0.8);
                context.quadraticCurveTo(s * 0.7, 0, 0, s * 0.8);
                context.quadraticCurveTo(-s * 0.7, 0, 0, -s * 0.8);
                context.fill();
                context.strokeStyle = 'rgba(0, 0, 0, .25)';
                context.lineWidth = 1;
                context.beginPath();
                context.moveTo(0, -s * 0.8);
                context.lineTo(0, s);
                context.stroke();
            }
        }

        resize();
        window.addEventListener('resize', resize);

        var count = Math.min(110, Math.round(window.innerWidth / 10));
        for (var i = 0; i < count; i++) {
            pieces.push(createPiece(true));
        }

        function frame() {
            if (!running) { return; }
            var elapsed = Date.now() - started;
            var fading = elapsed > duration;

            context.clearRect(0, 0, window.innerWidth, window.innerHeight);

            for (var i = pieces.length - 1; i >= 0; i--) {
                var p = pieces[i];
                p.y += p.speed;
                p.wobble += 0.05;
                p.x += p.drift + Math.sin(p.wobble) * 0.9;
                p.rotation += p.spin;

                if (p.y > window.innerHeight + 30) {
                    if (fading) {
                        pieces.splice(i, 1);
                        continue;
                    }
                    pieces[i] = createPiece(true);
                    continue;
                }

                context.save();
                context.translate(p.x, p.y);
                context.rotate(p.rotation);
                draw(p);
                context.restore();
            }

            if (pieces.length === 0) {
                running = false;
                context.clearRect(0, 0, window.innerWidth, window.innerHeight);
                window.removeEventListener('resize', resize);
                if (canvas.parentNode) { canvas.parentNode.removeChild(canvas); }
                return;
            }

            window.requestAnimationFrame(frame);
        }

        window.requestAnimationFrame(frame);
    }

    window.pdshConfetti = launch;

    document.addEventListener('DOMContentLoaded', function () {
        if (document.querySelector('[data-confetti]')) {
            launch();
        }
    });
})();
