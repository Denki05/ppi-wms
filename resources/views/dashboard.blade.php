@extends('layouts.app')

@section('title', 'Beranda')

@push('styles')
<style>
    /* ================= HERO ================= */
    .hero {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 18px;
        flex-wrap: wrap;
        padding: 20px 24px;
        border-radius: 18px;
        background:
            radial-gradient(90% 140% at 100% 0%, rgba(20,184,166,.18) 0%, transparent 55%),
            radial-gradient(70% 120% at 0% 100%, rgba(37,99,235,.08) 0%, transparent 60%),
            linear-gradient(120deg, #ffffff 0%, #f0faf6 100%);
        border: 1px solid #dcefe7;
        box-shadow: 0 14px 30px -26px rgba(15,23,42,.45);
    }
    .hero-main { position: relative; z-index: 1; min-width: 0; }
    .hero-date {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 3px 10px;
        border-radius: 999px;
        background: #fff;
        border: 1px solid #dcefe7;
        font-size: 10.5px;
        font-weight: 600;
        color: #0f766e;
        margin-bottom: 9px;
    }
    .hero-date svg { width: 12px; height: 12px; }
    .hero-date span { color: #94a3b8; font-weight: 500; }
    .hero h1 {
        margin: 0 0 4px;
        font-size: 21px;
        letter-spacing: -.4px;
        color: #0b1f1a;
    }
    .hero p { margin: 0; font-size: 12.5px; color: #64748b; max-width: 560px; line-height: 1.5; }
    .hero p b { color: #0f766e; }

    .hero-side {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: stretch;
        gap: 9px;
        flex: none;
    }
    .hero-stat {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 18px 10px 14px;
        border-radius: 14px;
        background: #fff;
        border: 1px solid #dcefe7;
        box-shadow: 0 8px 18px -16px rgba(15,23,42,.4);
    }
    .hero-stat .ring {
        flex: none;
        width: 38px; height: 38px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        background: #eaf7f2;
        color: #0f766e;
    }
    .hero-stat .ring svg { width: 17px; height: 17px; }
    .hero-stat b {
        display: block;
        font-size: 26px;
        line-height: 1;
        color: #0b1f1a;
        font-variant-numeric: tabular-nums;
    }
    .hero-stat small {
        display: block;
        font-size: 10.5px;
        color: #64748b;
        margin-top: 3px;
    }
    .hero-refresh {
        display: grid;
        place-items: center;
        width: 40px;
        border-radius: 13px;
        background: #fff;
        border: 1px solid #dcefe7;
        color: #64748b;
        transition: background .15s ease, color .15s ease, border-color .15s ease;
    }
    .hero-refresh:hover { background: #f0fdfa; color: #0f766e; border-color: #b8e6d8; }
    .hero-refresh svg { width: 15px; height: 15px; }

    /* ================= BOARD ================= */
    .board {
        margin-top: 16px;
    }

    .panel-card {
        background: #fff;
        border: 1px solid #e6eeeb;
        border-radius: 18px;
        box-shadow: 0 1px 2px rgba(15,23,42,.03), 0 16px 30px -28px rgba(15,23,42,.4);
        overflow: hidden;
    }
    .panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 17px;
        border-bottom: 1px solid #f1f6f4;
    }
    .panel-title h2 { margin: 0; font-size: 13.5px; letter-spacing: -.15px; color: #0f172a; }
    .panel-title small { display: block; font-size: 10.5px; color: #94a3b8; margin-top: 2px; }
    .panel-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: 10.5px;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
        background: #fef3c7;
        color: #92400e;
        flex: none;
    }

    /* ---- daftar tugas ---- */
    .tasks { padding: 5px 8px 8px; }
    .task {
        position: relative;
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 10px;
        border-radius: 12px;
        transition: background .14s ease;
    }
    .task + .task { border-top: 1px solid #f4f8f6; }
    .task:hover { background: #f8fbfa; }
    .task:hover .task-go { color: var(--accent); transform: translateX(2px); }
    .task-icon {
        flex: none;
        width: 38px; height: 38px;
        border-radius: 11px;
        display: grid;
        place-items: center;
        background: var(--soft);
        color: var(--accent);
    }
    .task-icon svg { width: 18px; height: 18px; }
    .task-body { flex: 1; min-width: 0; }
    .task-module {
        display: block;
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: .45px;
        text-transform: uppercase;
        color: var(--accent);
        margin-bottom: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .task-body b {
        display: block;
        font-size: 13px;
        font-weight: 700;
        line-height: 1.25;
        color: #0f172a;
    }
    .task-body small {
        display: block;
        font-size: 10.5px;
        color: #94a3b8;
        margin-top: 1px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .task-num {
        flex: none;
        text-align: right;
        font-variant-numeric: tabular-nums;
        line-height: 1;
    }
    .task-num b { display: block; font-size: 19px; font-weight: 800; color: var(--accent); }
    .task-num small { display: block; font-size: 9px; color: #94a3b8; margin-top: 2px; }
    .task-go {
        flex: none;
        width: 15px; height: 15px;
        color: #cbd5e1;
        transition: color .14s ease, transform .14s ease;
    }

    .all-clear {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 22px 17px;
    }
    .all-clear .ring {
        flex: none;
        width: 40px; height: 40px;
        border-radius: 50%;
        display: grid; place-items: center;
        background: #ecfdf5;
        color: #059669;
    }
    .all-clear b { display: block; font-size: 13px; color: #065f46; }
    .all-clear small { color: #64748b; font-size: 11px; }

    /* ---- kinerja logistik: profil bulanan dengan filter bulan ---- */
    .month-pick {
        display: flex;
        align-items: center;
        gap: 7px;
        color: #64748b;
        font-size: 11px;
        font-weight: 600;
        flex: none;
    }
    .month-pick svg { width: 14px; height: 14px; }
    .month-pick select {
        appearance: none;
        -webkit-appearance: none;
        border: 1px solid #d8e1dc;
        background: #fff;
        border-radius: 9px;
        padding: 7px 28px 7px 11px;
        font-family: inherit;
        font-size: 12px;
        font-weight: 700;
        color: #0f172a;
        cursor: pointer;
        transition: border-color .14s ease, box-shadow .14s ease;
    }
    .month-pick select:hover { border-color: #b8e6d8; }
    .month-pick select:focus {
        outline: none;
        border-color: #14b8a6;
        box-shadow: 0 0 0 3px rgba(20,184,166,.15);
    }
    .month-pick .chev { position: relative; }
    .month-pick .chev::after {
        content: '';
        position: absolute;
        right: 11px;
        top: 50%;
        width: 7px; height: 7px;
        border-right: 1.8px solid #94a3b8;
        border-bottom: 1.8px solid #94a3b8;
        transform: translateY(-70%) rotate(45deg);
        pointer-events: none;
    }

    .perf-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        padding: 14px;
    }
    @media (max-width: 760px) { .perf-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .perf-grid { grid-template-columns: 1fr; } }
    .perf {
        padding: 13px 15px;
        border-radius: 14px;
        background: #fff;
        border: 1px solid #e6eeeb;
        transition: border-color .15s ease, box-shadow .15s ease;
    }
    .perf:hover { border-color: #b8e6d8; box-shadow: 0 10px 22px -20px rgba(15,23,42,.5); }
    .perf b {
        display: block;
        font-size: 23px;
        line-height: 1.1;
        color: #0f172a;
        font-variant-numeric: tabular-nums;
        letter-spacing: -.4px;
    }
    .perf small { display: block; font-size: 11px; color: #64748b; margin-top: 5px; line-height: 1.4; }
    .perf.hi {
        background: linear-gradient(130deg, #f4fbf8 0%, #ffffff 70%);
        border-color: #b8e6d8;
    }
    .perf em {
        display: block;
        font-style: normal;
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .5px;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 7px;
    }
    .perf.hi em { color: #0f766e; }
    .delta {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 6px;
        font-size: 10.5px;
        font-weight: 800;
        letter-spacing: .2px;
    }
    .delta.up { color: #059669; }
    .delta.down { color: #e11d48; }
    .delta.flip.up { color: #e11d48; }   /* kecepatan: naik itu buruk */
    .delta.flip.down { color: #059669; } /* kecepatan: turun itu bagus */
    .delta span { color: #94a3b8; font-weight: 600; }

    .task-age { display: block; font-size: 10px; color: #94a3b8; margin-top: 2px; }
    .task-age.warn { color: #b45309; font-weight: 700; }
    .late-row {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 4px 10px 0;
        padding: 10px 12px;
        border-radius: 10px;
        background: #fff7ed;
        border: 1px solid #fed7aa;
        color: #9a3412;
        font-size: 12px;
        line-height: 1.45;
    }
    .late-row > svg { width: 15px; height: 15px; flex: none; }
    .late-row b { color: #7c2d12; }
    .late-row .task-go { margin-left: auto; width: 13px; height: 13px; }

    .perf-foot {
        display: flex;
        flex-wrap: wrap;
        gap: 6px 18px;
        padding: 0 14px 14px;
        font-size: 11px;
        color: #64748b;
    }
    .perf-foot b { color: #0f172a; font-variant-numeric: tabular-nums; }

    /* ================= STATUS ================= */
    .error-note {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        background: #fffbeb;
        border: 1px solid #fde68a;
        border-radius: 12px;
        padding: 13px 15px;
        color: #92400e;
        font-size: 12px;
        margin-top: 16px;
    }
    .error-note a {
        font-weight: 700;
        background: #92400e;
        color: #fff;
        padding: 6px 12px;
        border-radius: 7px;
        font-size: 11px;
    }

    @media (max-width: 620px) {
        .hero { padding: 18px 16px; }
        .hero h1 { font-size: 18px; }
        .hero-side { width: 100%; }
        .hero-stat { flex: 1; }
        .task { padding: 11px 8px; }
        .task-num b { font-size: 17px; }
    }
</style>
@endpush

@section('content')
    @php
        $sections = config('wms.reminders', []);
        $grandTotal = 0;
        $pending = [];

        if ($summary) {
            foreach ($sections as $section) {
                foreach ($section['items'] as $item) {
                    $count = (int) data_get($summary, $item['key'], 0);
                    $grandTotal += $count;
                    if ($count > 0) {
                        $pending[] = [
                            'key'   => $item['key'],
                            'menu'  => $section['menu'],
                            'label' => $item['label'],
                            'hint'  => $item['hint'],
                            'count' => $count,
                            'moduleLabel' => $section['label'],
                        ];
                    }
                }
            }
        }

        // Aksen visual per menu; caption tetap mengikuti Trans.
        $meta = [
            'do'        => ['color' => '#0f766e', 'soft' => '#eaf7f2', 'icon' => '<path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 14l2 2 4-4"/>'],
            'mutasi'    => ['color' => '#2563eb', 'soft' => '#edf3fe', 'icon' => '<path d="M7 4v13"/><path d="M4 14l3 3 3-3"/><path d="M17 20V7"/><path d="M14 10l3-3 3 3"/>'],
            'receiving' => ['color' => '#7c3aed', 'soft' => '#f2edfe', 'icon' => '<path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/>'],
            'komplain'  => ['color' => '#e11d48', 'soft' => '#fdeef2', 'icon' => '<path d="M12 9v4"/><path d="M12 17h.01"/><path d="M10.3 3.9L1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>'],
            'stock'     => ['color' => '#b45309', 'soft' => '#fdf5e7', 'icon' => '<rect x="3" y="3" width="18" height="5" rx="1"/><path d="M5 8v11a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8"/><path d="M10 12h4"/>'],
        ];
        $fallback = ['color' => '#475569', 'soft' => '#f1f5f9', 'icon' => '<circle cx="12" cy="12" r="9"/>'];

        // Baris pengingat langsung membuka tab/status yang sesuai di halamannya.
        $taskLinks = [
            'do.belum_print'         => ['route' => 'delivery_order', 'params' => ['tab' => 'SPK', 'printed' => 0]],
            'do.siap_kirim'          => ['route' => 'delivery_order', 'params' => ['tab' => 'CETAK']],
            'do.belum_resi'          => ['route' => 'delivery_order', 'params' => ['tab' => 'RESI']],
            'mutasi.perlu_cek'       => ['route' => 'mutasi', 'params' => ['tab' => 'AKTIF']],
            'mutasi.belum_diambil'   => ['route' => 'mutasi', 'params' => ['tab' => 'BELUM']],
            'receiving.qc'           => ['route' => 'receiving', 'params' => ['status' => 'QC']],
            'receiving.menunggu_acc' => ['route' => 'receiving', 'params' => ['status' => 'READY']],
            'komplain.menunggu_acc'  => ['route' => 'komplain', 'params' => ['status' => 'ACTIVE']],
        ];

        $now = \Carbon\Carbon::now('Asia/Jakarta');
        $hari = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];
        $bulan = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $updated = $summary && !empty($summary['generated_at'])
            ? \Carbon\Carbon::parse($summary['generated_at'])->setTimezone('Asia/Jakarta')->format('H:i')
            : null;

        $hour = (int) $now->format('G');
        $greeting = $hour < 11 ? 'pagi' : ($hour < 15 ? 'siang' : ($hour < 19 ? 'sore' : 'malam'));
        $menusWithWork = count(array_unique(array_column($pending, 'menu')));

        // Angka kinerja: hari rata-rata, format "1,2 hari".
        $durasiTeks = function ($value) {
            if ($value === null) {
                return '-';
            }
            $text = number_format((float) $value, 1, ',', '.');
            return rtrim(rtrim($text, '0'), ',') . ' hari';
        };

        // Kinerja DO: riwayat 6 bulan + aging dokumen pending.
        $perf = is_array($performance) ? $performance : null;
        $months = $perf['months'] ?? [];
        $kunci = array_keys($months);
        rsort($kunci); // Y-m terbaru dulu
        $kunciUrut = collect(array_values($kunci));
        $bulanKini = $kunciUrut->first();
        $agings = $perf['aging'] ?? [];
        $jobAges = [
            'do.belum_print' => 'list_spk',
            'do.siap_kirim'  => 'cetak_sj',
            'do.belum_resi'  => 'update_resi',
        ];
        $namaBulan = function ($ym) use ($bulan) {
            if (!$ym || !preg_match('/^\d{4}-(\d{2})$/', $ym, $m)) {
                return '-';
            }
            return ($bulan[(int) $m[1]] ?? '') . ' ' . substr($ym, 0, 4);
        };
        $bulanS = ['Januari' => 'Jan', 'Februari' => 'Feb', 'Maret' => 'Mar', 'April' => 'Apr', 'Mei' => 'Mei', 'Juni' => 'Jun', 'Juli' => 'Jul', 'Agustus' => 'Ags', 'September' => 'Sep', 'Oktober' => 'Okt', 'November' => 'Nov', 'Desember' => 'Des'];
        $namaBulanS = function ($ym) use ($bulanS, $bulan) {
            if (!$ym || !preg_match('/^\d{4}-(\d{2})$/', $ym, $m)) {
                return '-';
            }
            return $bulanS[$bulan[(int) $m[1]]] ?? $bulan[(int) $m[1]];
        };
        $angka = function ($value) {
            return number_format((float) $value, 0, ',', '.');
        };
        // Delta perseentase vs bulan sebelumnya; null kalau pembanding kosong.
        $deltaPct = function ($cur, $prev) {
            if (!is_numeric($cur) || !is_numeric($prev) || (float) $prev == 0.0) {
                return null;
            }
            return (int) round(100 * ($cur - $prev) / max(1.0, (float) $prev));
        };
        // Selisih persen-poin (untuk metrik yang sudah berupa %).
        $deltaPoin = function ($cur, $prev) {
            if (!is_numeric($cur) || !is_numeric($prev)) {
                return null;
            }
            return (int) round($cur - $prev);
        };
    @endphp

    <section class="hero">
        <div class="hero-main">
            <span class="hero-date">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                {{ $hari[$now->format('l')] }}, {{ $now->day }} {{ $bulan[$now->month] }} {{ $now->year }}
                @if($updated) <span>· Diperbarui {{ $updated }} WIB</span> @endif
            </span>
            <h1>Selamat {{ $greeting }}, {{ $user['name'] ?? $user['username'] ?? 'User' }}</h1>
            <p>
                @if(!$summary)
                    Ringkasan pekerjaan gudang belum bisa dimuat.
                @elseif($grandTotal > 0)
                    Ada <b>{{ $grandTotal }} dokumen</b> menunggu di {{ $menusWithWork }} menu. Klik salah satu baris untuk langsung membuka menunya.
                @else
                    Semua tugas pada daftar ini sudah selesai. Tidak ada yang perlu dikerjakan.
                @endif
            </p>
        </div>
        @if($summary)
            <div class="hero-side">
                <div class="hero-stat">
                    <span class="ring">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                    </span>
                    <span>
                        <b>{{ $grandTotal }}</b>
                        <small>dokumen menunggu</small>
                    </span>
                </div>
                <a href="{{ route('dashboard', ['fresh' => 1]) }}" class="hero-refresh" title="Muat ulang" aria-label="Muat ulang">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 3v6h-6"/></svg>
                </a>
            </div>
        @endif
    </section>

    @if(!$summary)
        <div class="error-note">
            <span>{{ $summaryError ?? 'Pengingat belum bisa dimuat.' }}</span>
            <a href="{{ route('dashboard', ['fresh' => 1]) }}">Coba lagi</a>
        </div>
    @else
        <div class="board">
            <section class="panel-card">
                <header class="panel-head">
                    <div class="panel-title">
                        <h2>Perlu dikerjakan</h2>
                        <small>Diurutkan sesuai menu di Trans</small>
                    </div>
                    <span class="panel-pill">{{ $grandTotal }} dokumen</span>
                </header>

                @if(!empty($agings['resi_late']['count']))
                    <a href="{{ route('delivery_order', ['tab' => 'RESI']) }}" class="late-row">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.3 3.9L1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                        <span><b>{{ (int) $agings['resi_late']['count'] }} DO</b> sudah berangkat lebih dari 5 hari, resi belum terisi. Terlama {{ (int) $agings['resi_late']['oldest_days'] }} hari ({{ $agings['resi_late']['oldest_code'] }}).</span>
                        <svg class="task-go" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
                    </a>
                @endif
                @if(empty($pending))
                    <div class="all-clear">
                        <span class="ring">
                            <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                        </span>
                        <span>
                            <b>Tidak ada tugas tertunda</b>
                            <small>Semua dokumen pada daftar ini sudah selesai.</small>
                        </span>
                    </div>
                @else
                    <div class="tasks">
                        @foreach($pending as $task)
                            @php($m = $meta[$task['menu']] ?? $fallback)
                            @php($target = $taskLinks[$task['key']] ?? null)
                            @php($aging = $agings[$jobAges[$task['key']] ?? ''] ?? null)
                            <a href="{{ $target ? route($target['route'], $target['params']) : route('page', $task['menu']) }}" class="task" style="--accent: {{ $m['color'] }}; --soft: {{ $m['soft'] }};">
                                <span class="task-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round">{!! $m['icon'] !!}</svg>
                                </span>
                                <span class="task-body">
                                    <span class="task-module">{{ $task['moduleLabel'] }}</span>
                                    <b>{{ $task['label'] }}</b>
                                    <small>{{ $task['hint'] }}</small>
                                    @if(!empty($aging['oldest_days']))
                                        <small class="task-age{{ (int) $aging['oldest_days'] >= 2 ? ' warn' : '' }}">
                                            Terlama {{ (int) $aging['oldest_days'] }} hari &middot; {{ $aging['oldest_code'] }}
                                        </small>
                                    @endif
                                </span>
                                <span class="task-num">
                                    <b>{{ $task['count'] }}</b>
                                    <small>dok</small>
                                </span>
                                <svg class="task-go" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
                            </a>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    @endif

    {{-- Kinerja logistik: profil bulanan + filter bulan (6 bulan terakhir) --}}
    <section class="board">
        <section class="panel-card">
            <header class="panel-head">
                <div class="panel-title">
                    <h2>Kinerja logistik</h2>
                    <small>DO dari dibuat sampai berangkat, lalu update resi</small>
                </div>
                @if(count($months) > 0)
                    <div class="month-pick" title="Pilih bulan">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
                        <span class="chev">
                            <select id="perf-month" aria-label="Pilih bulan">
                                @foreach($kunciUrut as $ym)
                                    <option value="{{ $ym }}" @if($ym === $bulanKini) selected @endif>
                                        {{ $namaBulan($ym) }}{{ $ym === $bulanKini ? ' (berjalan)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </span>
                    </div>
                @endif
            </header>
            @if(count($months) === 0)
                <div class="all-clear">
                    <span class="ring" style="background:#fffbeb;color:#b45309;">
                        <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><path d="M12 16h.01"/></svg>
                    </span>
                    <span>
                        <b>Statistik kinerja belum bisa dimuat</b>
                        <small>Refresh halaman, kalau masih begini hubungi admin.</small>
                    </span>
                </div>
            @else
                @foreach($kunciUrut as $i => $ym)
                    @php($m = $months[$ym])
                    @php($prevYm = $kunciUrut[$i + 1] ?? null)
                    @php($prev = $prevYm ? $months[$prevYm] : null)
                    @php($prevNama = $prevYm ? $namaBulanS($prevYm) : null)
                    <div class="perf-month" data-perf-month="{{ $ym }}" @if($ym !== $bulanKini) style="display: none;" @endif>
                        <div class="perf-grid">
                            {{-- DO dibuat --}}
                            <div class="perf">
                                <em>DO dibuat</em>
                                <b>{{ $angka($m['created'] ?? 0) }}</b>
                                @if(!is_null($d = $deltaPct($m['created'] ?? null, $prev['created'] ?? null)))
                                    <span class="delta {{ $d >= 0 ? 'up' : 'down' }}">{{ $d >= 0 ? '▲' : '▼' }} {{ abs($d) }}% <span>vs {{ $prevNama }}</span></span>
                                @endif
                            </div>
                            {{-- DO berangkat --}}
                            <div class="perf">
                                <em>DO berangkat</em>
                                <b>{{ $angka($m['depart'] ?? 0) }}</b>
                                @if(!is_null($d = $deltaPct($m['depart'] ?? null, $prev['depart'] ?? null)))
                                    <span class="delta {{ $d >= 0 ? 'up' : 'down' }}">{{ $d >= 0 ? '▲' : '▼' }} {{ abs($d) }}% <span>vs {{ $prevNama }}</span></span>
                                @endif
                            </div>
                            {{-- Selesai resi --}}
                            <div class="perf">
                                <em>Selesai (update resi)</em>
                                <b>{{ $angka($m['resi_done'] ?? 0) }}</b>
                                <small>dari {{ $angka($m['depart'] ?? 0) }} DO berangkat</small>
                            </div>
                            {{-- Kecepatan rata-rata --}}
                            <div class="perf hi">
                                <em>Kecepatan rata-rata</em>
                                <b>{{ $durasiTeks($m['avg_days_to_depart'] ?? null) }}</b>
                                @if(!is_null($d = $deltaPct($m['avg_days_to_depart'] ?? null, $prev['avg_days_to_depart'] ?? null)))
                                    <span class="delta flip {{ $d >= 0 ? 'up' : 'down' }}">{{ $d >= 0 ? '▲' : '▼' }} {{ abs($d) }}% <span>vs {{ $prevNama }}</span></span>
                                @endif
                            </div>
                            {{-- Berangkat <= 1 hari --}}
                            <div class="perf hi">
                                <em>Berangkat &le; 1 hari</em>
                                <b>{{ is_numeric($m['within_1day_pct'] ?? null) ? ((int) $m['within_1day_pct']) . '%' : '-' }}</b>
                                <small>{{ (int) ($m['within_1day'] ?? 0) }} dari {{ (int) ($m['depart'] ?? 0) }} dok</small>
                                @if(!is_null($d = $deltaPoin($m['within_1day_pct'] ?? null, $prev['within_1day_pct'] ?? null)))
                                    <span class="delta {{ $d >= 0 ? 'up' : 'down' }}">{{ $d >= 0 ? '▲' : '▼' }} {{ abs($d) }} poin <span>vs {{ $prevNama }}</span></span>
                                @endif
                            </div>
                            {{-- Berangkat -> resi --}}
                            <div class="perf">
                                <em>Berangkat &rarr; resi</em>
                                <b>{{ $durasiTeks($m['avg_days_resi_lag'] ?? null) }}</b>
                                @if(!is_null($d = $deltaPct($m['avg_days_resi_lag'] ?? null, $prev['avg_days_resi_lag'] ?? null)))
                                    <span class="delta flip {{ $d >= 0 ? 'up' : 'down' }}">{{ $d >= 0 ? '▲' : '▼' }} {{ abs($d) }}% <span>vs {{ $prevNama }}</span></span>
                                @endif
                            </div>
                        </div>
                        <div class="perf-foot">
                            @if(!empty($m['fastest']['code']))
                                <span>Tercepat: <b>{{ $m['fastest']['code'] }}</b> ({{ $durasiTeks($m['fastest']['hari']) }})</span>
                            @endif
                            @if($ym === $bulanKini)
                                <span>Bulan berjalan &mdash; data mengikuti tanggal berjalan</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </section>
    </section>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var sel = document.getElementById('perf-month');
        if (!sel) return;
        sel.addEventListener('change', function () {
            document.querySelectorAll('[data-perf-month]').forEach(function (el) {
                el.style.display = el.getAttribute('data-perf-month') === sel.value ? '' : 'none';
            });
        });
    });
    </script>
@endsection
