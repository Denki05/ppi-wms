<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'WMS Gudang') — SPV Logistik</title>
    <style id="wmsBaseStyle">
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background:
                radial-gradient(circle at 10% 0%, rgba(15,118,110,.07), transparent 42%),
                radial-gradient(circle at 95% 8%, rgba(37,99,235,.05), transparent 38%),
                #f6f9f8;
            color: #1e293b;
        }
        a { color: inherit; text-decoration: none; }

        /* ============ HEADER ============ */
        .topbar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255,255,255,.92);
            backdrop-filter: blur(10px);
            color: #0f172a;
            border-bottom: 1px solid #e6eeeb;
            box-shadow: 0 1px 2px rgba(15,23,42,.03);
        }
        .topbar-inner {
            max-width: 1180px;
            margin: 0 auto;
            padding: 8px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
        }
        .brand { display: flex; align-items: center; gap: 8px; font-weight: 700; font-size: 14.5px; flex: none; color: #0f172a; }
        .brand-mark {
            width: 26px; height: 26px; border-radius: 7px;
            display: grid; place-items: center;
            background: linear-gradient(135deg, #14b8a6, #0f766e);
            font-size: 9.5px; font-weight: 800; letter-spacing: .4px;
        }
        .brand small { display: block; font-size: 10px; font-weight: 400; color: #64748b; line-height: 1.1; }

        .mainnav {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 2px;
            row-gap: 3px;
            flex: 1 1 auto;
            min-width: 0;
        }
        .navlink {
            flex: none;
            padding: 6px 11px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            color: #52616f;
            white-space: nowrap;
            transition: background .12s ease, color .12s ease;
        }
        .navlink:hover { background: #f1f7f5; color: #0f766e; }
        .navlink.active { background: #e8f7f1; color: #0f766e; }

        /* Menu utama: Checker | Receiving | Inventory (dropdown) */
        .navdrop { position: relative; flex: none; margin-left: 2px; }
        .navdrop-toggle {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border: 1px solid #e6eeeb;
            border-radius: 8px;
            background: #f8fbfa;
            color: #52616f;
            font-family: inherit;
            font-size: 12px;
            font-weight: 600;
            line-height: 1.4;
            cursor: pointer;
            transition: background .12s ease, color .12s ease, border-color .12s ease;
        }
        .navdrop-toggle:hover,
        .navdrop.open .navdrop-toggle { background: #f1f7f5; color: #0f766e; }
        .navdrop-toggle.active { background: #e8f7f1; color: #0f766e; border-color: #cfeee4; }
        .navdrop-toggle svg { transition: transform .15s ease; }
        .navdrop.open .navdrop-toggle svg { transform: rotate(180deg); }

        .navdrop-menu {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            z-index: 30;
            display: none;
            min-width: 210px;
            padding: 5px;
            background: #fff;
            border: 1px solid #e6eeeb;
            border-radius: 12px;
            box-shadow: 0 14px 32px -10px rgba(15,23,42,.22), 0 2px 6px rgba(15,23,42,.05);
        }
        .navdrop.open .navdrop-menu { display: block; }
        .navdrop-item {
            display: block;
            padding: 7px 10px;
            border-radius: 8px;
            font-size: 12.5px;
            font-weight: 600;
            color: #475569;
            white-space: nowrap;
            transition: background .12s ease, color .12s ease;
        }
        .navdrop-item:hover { background: #f1f7f5; color: #0f766e; }
        .navdrop-item.active { background: #e8f7f1; color: #0f766e; }

        .topbar-actions { display: flex; align-items: center; gap: 8px; flex: none; }

        /* ============ NOTIFIKASI ============ */
        .sr-only {
            position: absolute; width: 1px; height: 1px;
            padding: 0; margin: -1px; overflow: hidden;
            clip: rect(0 0 0 0); white-space: nowrap; border: 0;
        }
        .notif-wrap { position: relative; flex: none; }
        .notif-btn {
            position: relative;
            width: 32px; height: 32px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 9px;
            border: 1px solid #e2e8e5;
            background: #fff;
            color: #475569;
            cursor: pointer;
            transition: background .15s ease, color .15s ease, border-color .15s ease;
        }
        .notif-btn:hover { background: #f7faf9; color: #0f766e; }
        .notif-btn.open { background: #e8f7f1; border-color: #bfe6da; color: #0f766e; }
        .notif-btn svg { width: 16px; height: 16px; }
        .notif-btn.has-unread svg { color: #b45309; }
        .notif-badge {
            position: absolute; top: -6px; right: -6px;
            min-width: 17px; height: 17px; padding: 0 4px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 999px;
            background: #dc2626; color: #fff;
            font-size: 10px; font-weight: 700; line-height: 1;
            box-shadow: 0 0 0 2px #fff;
            animation: notifPulse 2s ease-in-out infinite;
        }
        .notif-badge[hidden] { display: none; }
        @keyframes notifPulse {
            0%, 100% { transform: scale(1); }
            12%      { transform: scale(1.18); }
            24%      { transform: scale(1); }
        }
        @media (prefers-reduced-motion: reduce) {
            .notif-badge { animation: none; }
        }
        .notif-panel {
            position: absolute; top: calc(100% + 10px); right: 0;
            width: 340px; max-width: calc(100vw - 24px);
            background: #fff;
            border: 1px solid #e6eeeb;
            border-radius: 14px;
            box-shadow: 0 18px 40px -14px rgba(15,23,42,.28), 0 2px 8px rgba(15,23,42,.06);
            overflow: hidden;
            z-index: 60;
        }
        .notif-panel[hidden] { display: none; }
        .notif-head {
            display: flex; align-items: center; justify-content: space-between; gap: 8px;
            padding: 11px 14px;
            border-bottom: 1px solid #eef4f1;
        }
        .notif-head b { display: block; font-size: 13px; color: #0f172a; }
        .notif-head small { display: block; font-size: 10.5px; color: #94a3b8; margin-top: 1px; }
        .notif-head a { font-size: 11.5px; font-weight: 700; color: #0f766e; white-space: nowrap; }
        .notif-head a:hover { text-decoration: underline; }
        .notif-head-actions { display: flex; align-items: center; gap: 10px; }
        .notif-sound {
            display: flex; align-items: center; justify-content: center;
            width: 22px; height: 22px; padding: 0;
            border: 0; border-radius: 6px; background: none;
            color: #64748b; cursor: pointer;
        }
        .notif-sound:hover { background: #f1f7f5; color: #0f766e; }
        .notif-sound.muted { color: #cbd5e1; }
        .notif-sound svg { width: 14px; height: 14px; }
        main.wms-live-busy { opacity: .55; pointer-events: none; transition: opacity .15s ease; }
        /* Indikator memuat halaman tanpa reload. */
        .wms-progress {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 3px;
            z-index: 9999;
            overflow: hidden;
            opacity: 0;
            pointer-events: none;
            transition: opacity .18s ease;
        }
        .wms-progress.on { opacity: 1; }
        .wms-progress::before {
            content: '';
            position: absolute;
            top: 0; bottom: 0;
            width: 38%;
            border-radius: 999px;
            background: linear-gradient(90deg, #14b8a6, #0f766e, #14b8a6);
            box-shadow: 0 0 8px rgba(20,184,166,.55);
            animation: wmsProgress 1.05s ease-in-out infinite;
        }
        @keyframes wmsProgress {
            0%   { transform: translateX(-110%); }
            100% { transform: translateX(365%); }
        }
        @media (prefers-reduced-motion: reduce) {
            .wms-progress::before { animation-duration: 2s; }
        }
        /* Splash kecil saat halaman dibuka (full load). */
        .wms-boot {
            position: fixed;
            top: 0; right: 0; bottom: 0; left: 0;
            z-index: 9998;
            display: grid;
            place-items: center;
            background: #f6f9f8;
            opacity: 1;
            transition: opacity .22s ease;
        }
        .wms-boot.off { opacity: 0; pointer-events: none; }
        .wms-boot span {
            width: 28px; height: 28px;
            border-radius: 50%;
            border: 3px solid #d7e9e3;
            border-top-color: #0f766e;
            animation: wmsSpin .7s linear infinite;
        }
        @keyframes wmsSpin { to { transform: rotate(360deg); } }
        @media (prefers-reduced-motion: reduce) {
            .wms-boot span { animation-duration: 1.6s; }
        }
        .notif-list { max-height: min(62vh, 380px); overflow: auto; }
        .notif-item {
            display: flex; align-items: flex-start; gap: 10px;
            padding: 10px 14px;
            border-top: 1px solid #f2f7f5;
            color: inherit;
            transition: background .12s ease;
        }
        .notif-item:first-child { border-top: 0; }
        .notif-item:hover { background: #f8fbfa; }
        .notif-item.unread { background: #f5fcfa; }
        .notif-ico {
            flex: none; width: 28px; height: 28px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            background: #eef2f7; color: #64748b;
        }
        .notif-ico svg { width: 14px; height: 14px; }
        .notif-item[data-priority="action"] .notif-ico { background: #fff7e6; color: #b45309; }
        .notif-main { flex: 1; min-width: 0; }
        .notif-title { font-size: 12.5px; font-weight: 600; color: #0f172a; line-height: 1.35; }
        .notif-item.unread .notif-title { font-weight: 700; }
        .notif-sub { font-size: 11px; color: #64748b; margin-top: 1px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .notif-time { flex: none; font-size: 10px; color: #94a3b8; white-space: nowrap; padding-top: 2px; }
        .notif-empty { padding: 22px 14px; text-align: center; color: #94a3b8; font-size: 12px; }
        .notif-foot {
            display: flex; align-items: center; justify-content: space-between; gap: 8px;
            padding: 9px 14px;
            border-top: 1px solid #eef4f1;
            background: #fbfdfc;
        }
        .notif-foot a { font-size: 11.5px; font-weight: 700; color: #0f766e; }
        .notif-foot form { margin: 0; }
        .notif-foot button {
            border: 0; background: none; padding: 0; cursor: pointer;
            font-size: 11.5px; font-weight: 600; color: #64748b;
        }
        .notif-foot button:hover { color: #b45309; }
        .notif-foot button:disabled { color: #cbd5e1; cursor: default; }
        @media (max-width: 620px) {
            .notif-panel { position: fixed; top: 58px; left: 10px; right: 10px; width: auto; max-width: none; }
        }
        .who { text-align: right; font-size: 12px; line-height: 1.25; color: #0f172a; margin-right: 2px; }
        .who small { display: block; font-size: 10px; color: #64748b; }

        .badge {
            display: inline-block;
            padding: 1px 7px;
            border-radius: 999px;
            font-size: 9.5px;
            font-weight: 700;
            letter-spacing: .4px;
            text-transform: uppercase;
            background: #f59e0b;
            color: #1f2937;
            vertical-align: middle;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 6px 11px;
            border-radius: 8px;
            border: 1px solid transparent;
            font-size: 11.5px;
            font-weight: 600;
            cursor: pointer;
            background: #f1f5f3;
            color: #334155;
            border-color: #e2e8e5;
            transition: background .15s ease;
        }
        .btn:hover { background: #e8eeeb; }
        .btn-outline { background: #fff; border-color: #dbe4e0; color: #475569; }
        .btn-outline:hover { background: #f7faf9; }
        .btn-teal { background: linear-gradient(135deg, #14b8a6, #0f766e); color: #fff; border-color: transparent; }
        .btn-teal:hover { background: linear-gradient(135deg, #0f766e, #115e59); }

        /* ============ CONTENT ============ */
        main { flex: 1; width: 100%; max-width: 1180px; margin: 0 auto; padding: 22px 18px 40px; }

        .page-head { margin-bottom: 16px; }
        .page-head h1 { margin: 0 0 3px; font-size: 19px; }
        .page-head p { margin: 0; color: #64748b; font-size: 13px; }

        .head-row {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 12px;
            flex-wrap: wrap;
        }
        .sync-line { font-size: 11.5px; color: #94a3b8; text-align: right; }
        .sync-line a { color: #0f766e; font-weight: 600; }

        .section-title {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 22px 0 10px;
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .5px;
            text-transform: uppercase;
            color: #475569;
        }
        .section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #e2e8f0;
        }

        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(210px, 1fr));
            gap: 10px;
        }
        .kpi {
            display: flex;
            align-items: center;
            gap: 11px;
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 12px 14px;
            box-shadow: 0 1px 2px rgba(15,23,42,.04);
            transition: transform .12s ease, box-shadow .12s ease, border-color .12s ease;
        }
        .kpi:hover {
            transform: translateY(-1px);
            border-color: #99f6e4;
            box-shadow: 0 6px 14px rgba(15,118,110,.10);
        }
        .kpi-mark {
            flex: none;
            width: 34px; height: 34px;
            border-radius: 8px;
            display: grid; place-items: center;
            font-size: 11px; font-weight: 800; letter-spacing: .4px;
            background: #f1f5f9;
            color: #475569;
        }
        .kpi-body { min-width: 0; flex: 1; }
        .kpi-label { font-size: 12.5px; font-weight: 600; line-height: 1.25; }
        .kpi-hint { font-size: 10.5px; color: #94a3b8; margin-top: 1px; }
        .kpi-count {
            flex: none;
            min-width: 30px;
            text-align: right;
            font-size: 19px;
            font-weight: 800;
            color: #0f172a;
            font-variant-numeric: tabular-nums;
        }
        .kpi-count small { display: block; font-size: 9px; font-weight: 600; color: #94a3b8; }

        .kpi.urgent { border-left: 3px solid #f59e0b; }
        .kpi.urgent .kpi-mark { background: #fffbeb; color: #b45309; }
        .kpi.urgent .kpi-count { color: #b45309; }
        .kpi.clear { opacity: .68; }
        .kpi.clear .kpi-count { color: #16a34a; }

        .panel {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 1px 2px rgba(15,23,42,.05);
        }
        .panel h2 { margin: 0 0 6px; font-size: 16.5px; }
        .panel p { color: #64748b; font-size: 13px; }
        .breadcrumb { font-size: 12px; color: #64748b; margin-bottom: 10px; }
        .breadcrumb a { color: #0f766e; font-weight: 600; }
        .notice {
            margin-top: 14px;
            padding: 11px 13px;
            border-radius: 8px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            font-size: 12.5px;
        }
        @media (max-width: 760px) {
            .topbar-inner { padding: 8px 12px; gap: 8px; }
            .mainnav { order: 3; flex: 1 0 100%; padding-top: 2px; }
            .topbar-actions { margin-left: auto; }
            .who { display: none; }
            .navdrop-menu { min-width: 200px; }
            main { padding: 16px 12px 32px; }
        }
    </style>
    <link rel="stylesheet" href="{{ asset('vendor/sweetalert2/sweetalert2.min.css') }}">
    @stack('styles')
    <script src="{{ asset('vendor/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script>
    /* Mesin live WMS: nav/submit tanpa reload, toast SweetAlert, suara notifikasi. */
    (function () {
        if (window.Wms) { return; }

        var POLL_MS = 30000;
        var hooks = [];
        var bound = {};
        var busy = false;
        var polling = false;
        var dirty = false;
        var audioCtx = null;
        var toastMixin = null;

        function mainEl() { return document.getElementById('wmsContent'); }
        function hasSwal() { return typeof window.Swal !== 'undefined'; }

        var progressEl = null;
        var progressTimer = null;
        var progressCount = 0;

        function progressStart() {
            if (!progressEl) { progressEl = document.getElementById('wmsProgress'); }
            if (!progressEl) { return; }
            progressCount++;
            if (progressCount > 1) { return; }
            progressTimer = window.setTimeout(function () { progressEl.classList.add('on'); }, 90);
        }
        function progressStop() {
            if (progressCount > 0) { progressCount--; }
            if (progressCount > 0) { return; }
            window.clearTimeout(progressTimer);
            if (!progressEl) { progressEl = document.getElementById('wmsProgress'); }
            if (progressEl) { progressEl.classList.remove('on'); }
        }

        function hideBoot() {
            var el = document.getElementById('wmsBoot');
            if (!el || el.classList.contains('off')) { return; }
            el.classList.add('off');
            window.setTimeout(function () {
                if (el.parentNode) { el.parentNode.removeChild(el); }
            }, 260);
        }
        window.setTimeout(hideBoot, 6000);

        function soundEnabled() {
            try { return window.localStorage.getItem('wms_sound') !== 'off'; } catch (e) { return true; }
        }
        function updateSoundButton() {
            var btn = document.getElementById('notifSoundBtn');
            if (!btn) { return; }
            var on = soundEnabled();
            btn.classList.toggle('muted', !on);
            btn.setAttribute('aria-pressed', on ? 'true' : 'false');
            btn.setAttribute('title', on ? 'Matikan suara notifikasi' : 'Nyalakan suara notifikasi');
        }

        function ensureAudio() {
            var AC = window.AudioContext || window.webkitAudioContext;
            if (!AC) { return null; }
            if (!audioCtx) { audioCtx = new AC(); }
            if (audioCtx.state === 'suspended') { audioCtx.resume(); }
            return audioCtx;
        }

        /* Nada singkat ~0,22 detik, bukan alarm panjang. */
        function notifSound() {
            if (!soundEnabled()) { return; }
            var ctx = ensureAudio();
            if (!ctx) { return; }
            var t0 = ctx.currentTime + 0.01;
            [[880, 0, 0.09], [1245, 0.11, 0.09]].forEach(function (note) {
                var osc = ctx.createOscillator();
                var gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.value = note[0];
                gain.gain.setValueAtTime(0.0001, t0 + note[1]);
                gain.gain.exponentialRampToValueAtTime(0.18, t0 + note[1] + 0.012);
                gain.gain.exponentialRampToValueAtTime(0.0001, t0 + note[1] + note[2]);
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start(t0 + note[1]);
                osc.stop(t0 + note[1] + note[2] + 0.02);
            });
        }

        function toast(type, message) {
            if (!hasSwal() || !message) { return; }
            if (!toastMixin) {
                toastMixin = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3200,
                    timerProgressBar: true,
                    didOpen: function (el) {
                        el.addEventListener('mouseenter', Swal.stopTimer);
                        el.addEventListener('mouseleave', Swal.resumeTimer);
                    }
                });
            }
            toastMixin.fire({ icon: type || 'success', title: message });
        }

        function confirmAction(options) {
            var opts = typeof options === 'string' ? { text: options } : (options || {});
            if (!hasSwal()) {
                return Promise.resolve(window.confirm(opts.text || 'Lanjutkan?'));
            }
            return Swal.fire({
                title: opts.title || 'Konfirmasi',
                text: opts.text || '',
                icon: opts.icon || 'question',
                showCancelButton: true,
                confirmButtonText: opts.confirmText || 'Ya, lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                focusCancel: true,
                confirmButtonColor: '#0f766e',
                cancelButtonColor: '#94a3b8'
            }).then(function (result) { return !!result.isConfirmed; });
        }

        function alertAction(message, title, icon) {
            if (!hasSwal()) { window.alert(message); return; }
            Swal.fire({
                title: title || 'Perhatian',
                text: message,
                icon: icon || 'warning',
                confirmButtonText: 'Mengerti',
                confirmButtonColor: '#0f766e'
            });
        }

        function bindOnce(key, fn) {
            if (bound[key]) { return; }
            bound[key] = true;
            try { fn(); } catch (e) { /* halaman tanpa elemen terkait */ }
        }

        function runHooks() {
            var main = mainEl();
            if (!main) { return; }
            var list = hooks.slice();
            hooks.length = 0;
            list.forEach(function (fn) {
                try { fn(main); } catch (e) { /* abaikan hook halaman yang tidak cocok */ }
            });
        }

        function activateScripts(root) {
            Array.prototype.slice.call(root.querySelectorAll('script')).forEach(function (old) {
                var script = document.createElement('script');
                Array.prototype.forEach.call(old.attributes, function (attr) {
                    script.setAttribute(attr.name, attr.value);
                });
                script.text = old.text;
                old.parentNode.replaceChild(script, old);
            });
        }

        var lastFlash = { text: '', at: 0 };
        function flashToast(type, message) {
            var text = (message || '').replace(/\s+/g, ' ').trim();
            if (!text) { return; }
            var now = Date.now();
            if (text === lastFlash.text && now - lastFlash.at < 1500) { return; }
            lastFlash = { text: text, at: now };
            toast(type, text);
        }

        function handleFlash(main) {
            Array.prototype.slice.call(main.querySelectorAll('[data-flash]')).forEach(function (node) {
                var type = node.getAttribute('data-flash') === 'ok' ? 'success' : 'error';
                flashToast(type, node.textContent);
                node.parentNode.removeChild(node);
            });
        }

        function syncNav() {
            var nav = document.querySelector('.mainnav');
            if (!nav) { return; }
            var path = (location.pathname.replace(/\/+$/, '') || '/');
            var items = Array.prototype.slice.call(nav.querySelectorAll('.navdrop-item'));
            var best = null;
            var bestLen = -1;
            items.forEach(function (link) {
                var target = new URL(link.href, location.href).pathname.replace(/\/+$/, '');
                if ((path === target || path.indexOf(target + '/') === 0) && target.length > bestLen) {
                    best = link;
                    bestLen = target.length;
                }
            });
            items.forEach(function (link) { link.classList.toggle('active', link === best); });
            Array.prototype.forEach.call(nav.querySelectorAll('.navdrop'), function (drop) {
                var active = !!drop.querySelector('.navdrop-item.active');
                var toggle = drop.querySelector('.navdrop-toggle');
                if (toggle) { toggle.classList.toggle('active', active); }
            });
            var home = nav.querySelector('a.navlink[href]');
            if (home) { home.classList.toggle('active', path === '/'); }
        }

        /* CSS tiap halaman ada di <head> (push styles). Saat konten ditukar,
           blok style halaman lama harus diganti blok style halaman baru. */
        function syncPageStyles(doc) {
            Array.prototype.forEach.call(document.head.querySelectorAll('style[data-wms-page]'), function (node) {
                node.parentNode.removeChild(node);
            });
            if (!doc) { return; }
            Array.prototype.forEach.call(doc.head.querySelectorAll('style'), function (old) {
                if (old.id === 'wmsBaseStyle') { return; }
                var style = document.createElement('style');
                style.setAttribute('data-wms-page', '1');
                style.textContent = old.textContent;
                document.head.appendChild(style);
            });
        }

        function markPageStyles() {
            Array.prototype.forEach.call(document.head.querySelectorAll('style'), function (node) {
                if (node.id !== 'wmsBaseStyle') { node.setAttribute('data-wms-page', '1'); }
            });
        }

        function swapFrom(html, url, mode) {
            var doc = new DOMParser().parseFromString(html, 'text/html');
            var fresh = doc.getElementById('wmsContent');
            var main = mainEl();
            if (!fresh || !main) { window.location.href = url || location.href; return false; }

            syncPageStyles(doc);
            main.innerHTML = fresh.innerHTML;
            hooks.length = 0;
            document.body.style.overflow = '';
            document.body.classList.remove('stock-modal-open');
            activateScripts(main);

            if (doc.title) {
                document.title = doc.title;
                document.dispatchEvent(new CustomEvent('wms:swapped', { detail: { title: doc.title } }));
            }
            if (url) {
                if (mode === 'push') { history.pushState({ wms: true }, '', url); }
                else if (mode === 'replace') { history.replaceState({ wms: true }, '', url); }
            }
            syncNav();
            handleFlash(main);
            dirty = false;
            return true;
        }

        function fetchPage(url, silent) {
            var main = mainEl();
            if (main && !silent) { main.classList.add('wms-live-busy'); }
            if (!silent) { progressStart(); }
            function done() {
                if (main) { main.classList.remove('wms-live-busy'); }
                if (!silent) { progressStop(); }
            }
            return fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-WMS-Live': '1', 'Accept': 'text/html' },
                credentials: 'same-origin',
                cache: 'no-store'
            }).then(function (res) {
                if (!res.ok) { throw new Error('HTTP ' + res.status); }
                return res.text().then(function (html) {
                    return { html: html, url: res.url || url };
                });
            }).then(function (out) {
                done();
                return out;
            }, function (err) {
                done();
                throw err;
            });
        }

        function navigate(url, mode) {
            if (busy) { return; }
            busy = true;
            fetchPage(url, false).then(function (out) {
                swapFrom(out.html, out.url, mode);
            }).catch(function () {
                window.location.href = url;
            }).then(function () { busy = false; });
        }

        function isDirty() {
            if (dirty) { return true; }
            var main = mainEl();
            if (!main) { return true; }
            if (document.querySelector('.qc-modal.open, #stock-modal.open')) { return true; }
            var active = document.activeElement;
            if (active && main.contains(active) && /^(INPUT|SELECT|TEXTAREA)$/.test(active.tagName)) { return true; }
            var files = main.querySelectorAll('input[type="file"]');
            for (var i = 0; i < files.length; i++) {
                if (files[i].files && files[i].files.length) { return true; }
            }
            // Checklist yang dicentang lewat tombol (tanpa event change) juga harus aman.
            if (main.querySelector('input[type="checkbox"]:checked, input[type="radio"]:checked')) { return true; }
            return false;
        }

        function poll() {
            if (document.hidden || busy || polling || !mainEl() || isDirty()) { return; }
            polling = true;
            fetchPage(location.href, true).then(function (out) {
                var doc = new DOMParser().parseFromString(out.html, 'text/html');
                var fresh = doc.getElementById('wmsContent');
                var main = mainEl();
                if (!fresh || !main || fresh.innerHTML === main.innerHTML) { return; }
                swapFrom(out.html, location.href, 'none');
            }).catch(function () { /* polling diam saat koneksi putus */ })
              .then(function () { polling = false; });
        }

        function serializeForm(form, action) {
            var data = new FormData(form);
            var params = new URLSearchParams();
            data.forEach(function (value, key) {
                if (value !== '' && value !== null) { params.append(key, value); }
            });
            var base = action.split('#')[0];
            return base + (base.indexOf('?') === -1 ? '?' : '&') + params.toString();
        }

        function submitLive(form) {
            if (busy) { return; }
            var method = (form.getAttribute('method') || 'GET').toUpperCase();
            var action = form.getAttribute('action') || location.href;
            var submit = form.querySelector('[type="submit"]');
            var restore = null;
            if (submit) {
                restore = { html: submit.innerHTML, disabled: submit.disabled };
                submit.disabled = true;
                submit.textContent = 'Memproses…';
            }

            busy = true;
            var main = mainEl();
            if (main) { main.classList.add('wms-live-busy'); }
            progressStart();

            var request;
            if (method === 'GET') {
                request = fetch(serializeForm(form, action), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
                    credentials: 'same-origin'
                });
            } else {
                request = fetch(action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-WMS-Live': '1', 'Accept': 'text/html' },
                    credentials: 'same-origin'
                });
            }

            request.then(function (res) {
                if (!res.ok) { throw new Error('HTTP ' + res.status); }
                return res.text().then(function (html) {
                    return { html: html, url: res.url || action };
                });
            }).then(function (out) {
                if (main) { main.classList.remove('wms-live-busy'); }
                progressStop();
                swapFrom(out.html, out.url, method === 'GET' ? 'push' : 'replace');
            }).catch(function () {
                if (main) { main.classList.remove('wms-live-busy'); }
                progressStop();
                if (restore) { submit.innerHTML = restore.html; submit.disabled = restore.disabled; }
                toast('error', 'Aksi belum berhasil diproses. Halaman dimuat ulang.');
                window.setTimeout(function () { form.submit(); }, 900);
            }).then(function () { busy = false; });
        }

        window.Wms = {
            toast: toast,
            alert: alertAction,
            confirm: confirmAction,
            notifSound: notifSound,
            soundEnabled: soundEnabled,
            bindOnce: bindOnce,
            onSwap: function (fn) {
                hooks.push(fn);
                var main = mainEl();
                if (main && document.readyState !== 'loading') {
                    try { fn(main); } catch (e) { /* hook opsional */ }
                }
            },
            refresh: function () { poll(); },
            navigate: function (url) { navigate(url, 'push'); }
        };

        /* Navigasi <a> di dalam konten tanpa reload. */
        document.addEventListener('click', function (event) {
            if (event.defaultPrevented || event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) { return; }
            var link = event.target.closest ? event.target.closest('a[href]') : null;
            if (!link) { return; }
            if (link.hasAttribute('data-native')) { return; }
            var main = mainEl();
            if (!main || !link.closest('main, .topbar')) { return; }
            if (link.target && link.target !== '_self') { return; }
            if (link.hasAttribute('download')) { return; }
            var href = link.getAttribute('href');
            if (!href || href.charAt(0) === '#' || /^(javascript|mailto|tel):/i.test(href)) { return; }
            var url = new URL(link.href, location.href);
            if (url.origin !== location.origin) { return; }
            event.preventDefault();
            navigate(url.href, 'push');
        });

        /* Submit form: GET filter & POST aksi, semuanya tanpa reload. */
        document.addEventListener('submit', function (event) {
            var form = event.target;
            if (!form || form.tagName !== 'FORM' || event.defaultPrevented) { return; }

            if (!form.closest('main')) {
                if (form.closest('#notifWrap')) {
                    event.preventDefault();
                    fetch(form.action, {
                        method: 'POST',
                        body: new FormData(form),
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        credentials: 'same-origin'
                    }).then(function (res) { return res.text(); }).then(function (html) {
                        var doc = new DOMParser().parseFromString(html, 'text/html');
                        var flashes = doc.querySelectorAll('[data-flash]');
                        if (flashes.length) {
                            Array.prototype.forEach.call(flashes, function (node) {
                                flashToast(node.getAttribute('data-flash') === 'ok' ? 'success' : 'error', node.textContent);
                            });
                        }
                        document.dispatchEvent(new CustomEvent('wms:notif-refresh'));
                    }).catch(function () { form.submit(); });
                }
                return;
            }

            event.preventDefault();
            var message = form.getAttribute('data-confirm');
            if (message) {
                confirmAction({ text: message }).then(function (ok) { if (ok) { submitLive(form); } });
            } else {
                submitLive(form);
            }
        });

        document.addEventListener('input', function (event) {
            var main = mainEl();
            if (main && main.contains(event.target)) { dirty = true; }
        }, true);
        document.addEventListener('change', function (event) {
            var main = mainEl();
            if (main && main.contains(event.target)) { dirty = true; }
        }, true);

        window.addEventListener('popstate', function () { navigate(location.href, 'none'); });
        document.addEventListener('visibilitychange', function () { if (!document.hidden) { poll(); } });
        document.addEventListener('DOMContentLoaded', function () {
            hideBoot();
            markPageStyles();
            var boot = mainEl();
            if (boot) { handleFlash(boot); }
            syncNav();
            runHooks();
            updateSoundButton();
            var btn = document.getElementById('notifSoundBtn');
            if (btn) {
                btn.addEventListener('click', function (event) {
                    event.stopPropagation();
                    try {
                        window.localStorage.setItem('wms_sound', soundEnabled() ? 'off' : 'on');
                    } catch (e) { /* mode privat */ }
                    updateSoundButton();
                    if (soundEnabled()) { notifSound(); }
                });
            }
            window.setInterval(poll, POLL_MS);
        });

        document.addEventListener('click', function () { ensureAudio(); }, { once: true });
        document.addEventListener('keydown', function () { ensureAudio(); }, { once: true });
    })();
    </script>
</head>
<body>
    <div class="wms-boot" id="wmsBoot" aria-hidden="true"><span></span></div>
    <noscript><style>#wmsBoot{display:none}</style></noscript>
    <div class="wms-progress" id="wmsProgress" aria-hidden="true"></div>
    <header class="topbar">
        <div class="topbar-inner">
            <a href="{{ route('dashboard') }}" class="brand">
                <span class="brand-mark">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/>
                    </svg>
                </span>
                <span>
                    WMS Gudang
                    <small>SPV Logistik</small>
                </span>
            </a>

            @if(session()->has('wms.user'))
            @php
                // Satu menu bisa punya halaman daftar + detail + aksi.
                $menuRoutes = [
                    'do'           => ['delivery_order'],
                    'mutasi'       => ['mutasi'],
                    'receiving'    => ['receiving', 'receiving.show', 'receiving.acc'],
                    'receiving_qc' => ['receiving_transaksi'],
                    'komplain'     => ['komplain', 'komplain.show', 'komplain.acc'],
                    'stock'        => ['stock'],
                ];
                $currentMenu = null;
                foreach ($menuRoutes as $menuCode => $routeNames) {
                    if (request()->routeIs(...$routeNames)) {
                        $currentMenu = $menuCode;
                        break;
                    }
                }
            @endphp
            <nav class="mainnav">
                <a href="{{ route('dashboard') }}"
                   class="navlink {{ request()->routeIs('dashboard') ? 'active' : '' }}">Beranda</a>

                @foreach(config('wms.menu_groups', []) as $groupIndex => $group)
                    @php($groupActive = in_array($currentMenu, $group['codes'], true))
                    <div class="navdrop">
                        <button type="button" class="navdrop-toggle {{ $groupActive ? 'active' : '' }}"
                                id="navDropToggle{{ $groupIndex }}" aria-expanded="false" aria-haspopup="true"
                                aria-controls="navDropMenu{{ $groupIndex }}">
                            {{ $group['label'] }}
                            <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9l6 6 6-6"/></svg>
                        </button>
                        <div class="navdrop-menu" id="navDropMenu{{ $groupIndex }}" role="menu"
                             aria-labelledby="navDropToggle{{ $groupIndex }}">
                            @foreach($group['codes'] as $code)
                                @php($menu = config('wms.menus.' . $code))
                                @continue(!$menu)
                                <a href="{{ isset($menuRoutes[$code]) ? route($menuRoutes[$code][0]) : route('page', $code) }}"
                                   title="{{ $menu['label'] }}" role="menuitem"
                                   class="navdrop-item {{ $currentMenu === $code ? 'active' : '' }}">{{ $menu['short'] ?? $menu['label'] }}</a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>

            @php($wu = session('wms.user'))
            <div class="topbar-actions">
                <div class="notif-wrap" id="notifWrap"
                     data-feed="{{ route('notifications.feed') }}"
                     data-read="{{ url('/m/notifications') }}"
                     data-index="{{ route('notifications') }}">
                    <button type="button" class="notif-btn" id="notifBtn"
                            aria-haspopup="true" aria-expanded="false" aria-controls="notifPanel"
                            title="Notifikasi">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/>
                            <path d="M13.7 21a2 2 0 0 1-3.4 0"/>
                        </svg>
                        <span class="notif-badge" id="notifBadge" hidden></span>
                        <span class="sr-only" id="notifSr" aria-live="polite"></span>
                    </button>

                    <div class="notif-panel" id="notifPanel" role="dialog" aria-label="Notifikasi" hidden>
                        <div class="notif-head">
                            <div>
                                <b>Notifikasi</b>
                                <small id="notifSub">Memuat…</small>
                            </div>
                            <div class="notif-head-actions">
                                <button type="button" class="notif-sound" id="notifSoundBtn"
                                        aria-pressed="true" title="Matikan suara notifikasi">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                        <path d="M11 5L6 9H2v6h4l5 4V5z"/><path d="M15.5 8.5a5 5 0 0 1 0 7"/><path d="M18.5 5.5a9 9 0 0 1 0 13"/>
                                    </svg>
                                </button>
                                <a href="{{ route('notifications') }}">Lihat semua</a>
                            </div>
                        </div>
                        <div class="notif-list" id="notifList">
                            <div class="notif-empty">Memuat notifikasi…</div>
                        </div>
                        <div class="notif-foot">
                            <a href="{{ route('notifications') }}">Arsip notifikasi</a>
                            <form method="POST" action="{{ route('notifications.read_all') }}">
                                @csrf
                                <button type="submit" id="notifReadAll">Tandai semua dibaca</button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="who">
                    {{ $wu['name'] ?? $wu['username'] ?? '-' }}
                    @if(!empty($wu['is_overtake']))
                        <span class="badge">Overtake</span>
                    @endif
                    <small>{{ $wu['jabatan'] ?: ($wu['division'] ?: 'User WMS') }}</small>
                </div>
                {{-- Tombol kembali hanya untuk akun overtake (33). --}}
                @if(!empty($wu['is_overtake']))
                    <a href="{{ config('wms.trans_url') }}" class="btn btn-outline" data-native>Kembali ke Trans</a>
                @endif
                <a href="{{ route('logout') }}" class="btn btn-teal" data-native>Keluar</a>
            </div>
            @endif
        </div>
    </header>

    <main id="wmsContent">
        @if(session('ok'))<div data-flash="ok" hidden>{{ session('ok') }}</div>@endif
        @if(session('error'))<div data-flash="err" hidden>{{ session('error') }}</div>@endif
        @yield('content')
    </main>

    @if(session()->has('wms.user'))
    <script>
    (function () {
        var wrap = document.getElementById('notifWrap');
        if (!wrap) return;

        var btn    = document.getElementById('notifBtn');
        var panel  = document.getElementById('notifPanel');
        var list   = document.getElementById('notifList');
        var badge  = document.getElementById('notifBadge');
        var sub    = document.getElementById('notifSub');
        var sr     = document.getElementById('notifSr');
        var readAllBtn = document.getElementById('notifReadAll');

        var FEED  = wrap.getAttribute('data-feed');
        var READ  = wrap.getAttribute('data-read');
        var INDEX = wrap.getAttribute('data-index');
        var POLL_MS = 30000;
        var CLOCK_MS = 30000;
        var lastUnread = 0;
        var lastData = null;
        var firstLoad = true;
        var timer = null;
        var clock = null;
        var baseTitle = document.title;

        function iconSvg() {
            var svg = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
            svg.setAttribute('viewBox', '0 0 24 24');
            svg.setAttribute('fill', 'none');
            svg.setAttribute('stroke', 'currentColor');
            svg.setAttribute('stroke-width', '1.9');
            svg.setAttribute('stroke-linecap', 'round');
            svg.setAttribute('stroke-linejoin', 'round');
            var p1 = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            p1.setAttribute('d', 'M1 3h15v13H1z');
            var p2 = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            p2.setAttribute('d', 'M16 8h4l3 3v5h-7V8z');
            var p3 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            p3.setAttribute('cx', '5.5'); p3.setAttribute('cy', '18.5'); p3.setAttribute('r', '2.5');
            var p4 = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
            p4.setAttribute('cx', '18.5'); p4.setAttribute('cy', '18.5'); p4.setAttribute('r', '2.5');
            return svg;
        }

        function relTime(iso) {
            if (!iso) return '';
            var then = new Date(iso);
            if (isNaN(then.getTime())) return '';
            var diff = Math.max(0, Math.floor((Date.now() - then.getTime()) / 1000));
            if (diff < 60) return 'baru saja';
            if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
            if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
            if (diff < 2592000) return Math.floor(diff / 86400) + ' hari lalu';
            if (diff < 31536000) return Math.floor(diff / 2592000) + ' bulan lalu';
            return Math.floor(diff / 31536000) + ' tahun lalu';
        }

        function titleFor(item) {
            var status = parseInt(item.do_status, 10);
            if (status === 3) return 'DO ' + (item.code || '') + ' siap dikerjakan gudang';
            if (status === 6) return 'DO ' + (item.code || '') + ' selesai dikirim';
            if (status === 5) return 'DO ' + (item.code || '') + ' sedang dikirim';
            return 'DO ' + (item.code || '') + ' diperbarui';
        }

        function buildItem(item) {
            var a = document.createElement('a');
            a.className = 'notif-item' + (item.is_unread ? ' unread' : '');
            a.setAttribute('data-priority', item.priority || 'info');
            a.href = READ + '/' + encodeURIComponent(item.id) + '/read' + (item.do_id ? '?do=' + encodeURIComponent(item.do_id) : '');

            var ico = document.createElement('span');
            ico.className = 'notif-ico';
            ico.appendChild(iconSvg());

            var main = document.createElement('span');
            main.className = 'notif-main';

            var title = document.createElement('span');
            title.className = 'notif-title';
            title.textContent = titleFor(item);

            var subline = document.createElement('span');
            subline.className = 'notif-sub';
            var detail = item.customer || '-';
            if (item.city) detail += ' · ' + item.city;
            subline.textContent = detail;

            main.appendChild(title);
            main.appendChild(subline);

            var time = document.createElement('span');
            time.className = 'notif-time';
            time.textContent = relTime(item.created_at) || item.human || '';
            time.setAttribute('title', item.created_at || '');
            time.setAttribute('data-created', item.created_at || '');

            a.appendChild(ico);
            a.appendChild(main);
            a.appendChild(time);
            return a;
        }

        function render(data) {
            var items = (data && data.items) || [];
            var unread = (data && data.unread) || 0;
            var total = (data && data.total) || 0;

            list.textContent = '';

            if (!items.length) {
                var empty = document.createElement('div');
                empty.className = 'notif-empty';
                empty.textContent = 'Belum ada notifikasi.';
                list.appendChild(empty);
            } else {
                items.forEach(function (item) { list.appendChild(buildItem(item)); });
                if (total > items.length) {
                    var more = document.createElement('a');
                    more.className = 'notif-item';
                    more.href = INDEX;
                    more.style.justifyContent = 'center';
                    var moreText = document.createElement('span');
                    moreText.className = 'notif-title';
                    moreText.style.color = '#0f766e';
                    moreText.textContent = 'Lihat ' + (total - items.length) + ' notifikasi lainnya';
                    more.appendChild(moreText);
                    list.appendChild(more);
                }
            }

            sub.textContent = unread > 0
                ? unread + ' belum dibaca · ' + total + ' total'
                : total + ' notifikasi, semua sudah dibaca';

            badge.hidden = unread === 0;
            badge.textContent = unread > 99 ? '99+' : String(unread);
            btn.classList.toggle('has-unread', unread > 0);
            if (readAllBtn) readAllBtn.disabled = unread === 0;

            document.title = unread > 0 ? '(' + unread + ') ' + baseTitle : baseTitle;

            if (!firstLoad && unread > lastUnread) {
                if (window.Wms && window.Wms.notifSound) { window.Wms.notifSound(); }
                sr.textContent = unread + ' notifikasi belum dibaca.';
            }
            firstLoad = false;
            lastUnread = unread;
            lastData = data;
        }

        function fail(message) {
            list.textContent = '';
            var box = document.createElement('div');
            box.className = 'notif-empty';
            box.textContent = message || 'Notifikasi belum bisa dimuat.';
            list.appendChild(box);
            sub.textContent = 'Gagal memuat';
        }

        var loading = false;
        function load() {
            if (loading) return;
            loading = true;
            fetch(FEED, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
                .then(function (res) { return res.json(); })
                .then(function (json) {
                    if (!json || !json.ok) {
                        fail(json && json.message);
                        return;
                    }
                    render(json.data || {});
                })
                .catch(function () { fail('Koneksi ke Trans terputus.'); })
                .then(function () { loading = false; });
        }

        function startPolling() {
            stopPolling();
            timer = window.setInterval(load, POLL_MS);
            clock = window.setInterval(function () {
                var nodes = list.querySelectorAll('.notif-time[data-created]');
                Array.prototype.forEach.call(nodes, function (node) {
                    var text = relTime(node.getAttribute('data-created'));
                    if (text) node.textContent = text;
                });
            }, CLOCK_MS);
        }
        function stopPolling() {
            if (timer) { clearInterval(timer); timer = null; }
            if (clock) { clearInterval(clock); clock = null; }
        }

        function openPanel() {
            panel.hidden = false;
            btn.classList.add('open');
            btn.setAttribute('aria-expanded', 'true');
            load();
        }
        function closePanel() {
            panel.hidden = true;
            btn.classList.remove('open');
            btn.setAttribute('aria-expanded', 'false');
        }

        btn.addEventListener('click', function (event) {
            event.stopPropagation();
            if (panel.hidden) openPanel(); else closePanel();
        });

        panel.addEventListener('click', function (event) {
            event.stopPropagation();
        });

        document.addEventListener('click', function () { closePanel(); });
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') closePanel();
        });

        document.addEventListener('visibilitychange', function () {
            if (document.visibilityState === 'visible') load();
        });
        window.addEventListener('focus', load);
        document.addEventListener('wms:notif-refresh', load);
        document.addEventListener('wms:swapped', function (event) {
            baseTitle = (event.detail && event.detail.title) || document.title;
            if (lastUnread > 0) {
                document.title = '(' + lastUnread + ') ' + baseTitle;
            }
        });

        load();
        startPolling();
    })();
    </script>
    @endif

    <script>
    (function () {
        var drops = Array.prototype.slice.call(document.querySelectorAll('.navdrop'));
        if (!drops.length) return;

        function close(drop) {
            drop.classList.remove('open');
            drop.querySelector('.navdrop-toggle').setAttribute('aria-expanded', 'false');
        }

        drops.forEach(function (drop) {
            var toggle = drop.querySelector('.navdrop-toggle');

            toggle.addEventListener('click', function (event) {
                event.stopPropagation();
                var willOpen = !drop.classList.contains('open');
                drops.forEach(close);

                if (willOpen) {
                    drop.classList.add('open');
                    toggle.setAttribute('aria-expanded', 'true');
                }
            });
        });

        document.addEventListener('click', function (event) {
            // Pilih submenu: tutup dropdown walau halaman tidak di-reload.
            if (event.target.closest && event.target.closest('.navdrop-item')) {
                drops.forEach(close);
                return;
            }
            drops.forEach(function (drop) {
                if (!drop.contains(event.target)) close(drop);
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') drops.forEach(close);
        });
    })();
    </script>
</body>
</html>
