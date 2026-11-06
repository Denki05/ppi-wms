@extends('layouts.app')

@section('title', 'Checker Transaksi (DO)')

@push('styles')
@include('partials.list-styles')
<style>
    /* Checker DO: lebih padat, menyesuaikan layar */
    .do-table { font-size: 12.5px; }
    .do-table thead th { padding: 8px 10px; }
    .do-table tbody td { padding: 8px 10px; }
    .do-table .tag { margin-left: 0; margin-right: 3px; }

    .month-pick {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        height: 32px;
        padding: 0 9px;
        border: 1px solid #e3ece8;
        border-radius: 9px;
        background: #fff;
    }
    .month-pick span {
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: .4px;
        text-transform: uppercase;
        color: #94a3b8;
    }
    .month-pick select {
        font: inherit;
        font-size: 11.5px;
        font-weight: 600;
        color: #0f172a;
        border: 0;
        background: transparent;
        outline: none;
        padding: 0 2px;
        cursor: pointer;
        max-width: 140px;
    }

    .search-inline {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 0 1 340px;
        margin-left: auto;
        min-width: 0;
    }
    .search-inline input[type="text"] {
        flex: 1 1 auto;
        min-width: 0;
        font: inherit;
        font-size: 12px;
        padding: 7px 10px;
        border-radius: 8px;
        border: 1px solid #dbe6e1;
        background: #fff;
        color: #1e293b;
        outline: none;
        min-height: 34px;
    }
    .search-inline input[type="text"]:focus { border-color: #14b8a6; box-shadow: 0 0 0 3px rgba(20,184,166,.12); }
    .search-inline .btn { min-height: 34px; padding: 7px 12px; }
    .search-inline .btn-reset { min-height: 34px; }

    .pager .links { flex-wrap: wrap; align-items: center; }
    .pager .links .current {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 30px;
        padding: 5px 8px;
        border-radius: 7px;
        background: #0f766e;
        border: 1px solid #0f766e;
        color: #fff;
        font-weight: 700;
    }
    .pager .links .pg-gap { color: #94a3b8; padding: 0 2px; }
    .pg-text { display: inline; }

    @media (min-width: 1440px) {
        .do-table { font-size: 13px; }
        .do-table thead th { padding: 9px 12px; }
        .do-table tbody td { padding: 10px 12px; }
    }
    @media (max-width: 991px) {
        .do-table { font-size: 12px; }
        .do-table thead th { padding: 7px 8px; }
        .do-table tbody td { padding: 7px 8px; }
        .do-table .col-so { display: none; }
        .search-inline { flex: 0 1 260px; }
    }
    @media (max-width: 620px) {
        .search-inline { flex: 1 1 100%; margin-left: 0; }
        .search-inline input[type="text"] { width: 100%; }
        .dhead-side { width: 100%; justify-content: space-between; }
        .month-pick span { display: none; }
    }
    @media (max-width: 575px) {
        .do-table { font-size: 11.5px; }
        .do-table thead th { padding: 6px 7px; font-size: 9px; }
        .do-table tbody td { padding: 6px 7px; }
        .do-table .col-created { display: none; }
        .do-table .badge-status { padding: 2px 7px; font-size: 9.5px; }
        .do-table .tag { font-size: 8.5px; padding: 1px 5px; }
        .do-table .mini-btn { font-size: 10px; padding: 5px 7px; }
        .do-table .cell-date { font-size: 10.5px; }
        .pg-text { display: none; }
        .pager { padding: 9px 10px; }
        .pager .links a, .pager .links .current, .pager .links span.disabled { min-width: 30px; justify-content: center; padding: 5px 7px; }
    }
</style>
@endpush

@section('content')
    @php
        $updated = !empty($data['generated_at'])
            ? \Carbon\Carbon::parse($data['generated_at'])->setTimezone('Asia/Jakarta')->format('H:i')
            : null;
        $tabs = collect($data['tabs'] ?? [])->keyBy('tab');
        $rows = $data['rows'] ?? [];
        $meta = $data['meta'] ?? ['page' => 1, 'last_page' => 1, 'total' => 0];
        $activeTab = $data['tab'] ?? ($filters['tab'] ?? 'SPK');
        $onlyUnprinted = !empty($data['only_unprinted']);
        $labels = [
            'SPK'     => 'List SPK',
            'CETAK'   => 'Cetak SJ',
            'RESI'    => 'Update Resi',
            'HISTORY' => 'History Resi',
        ];
        $hints = [
            'SPK'     => 'DO siap dikerjakan, cetak SPK dulu',
            'CETAK'   => 'DO sudah dipacking, siap dibuat surat jalan',
            'RESI'    => 'DO dalam pengiriman, input resi',
            'HISTORY' => 'DO sudah diterima customer',
        ];
        $statusBadge = ['READY' => 'st-ready', 'PACKED' => 'st-qc', 'DELIVERING' => 'st-active', 'DELIVERED' => 'st-acc'];
        $currentMonth = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m');
        $rawMonth = (string) ($data['month'] ?? ($filters['month'] ?? ''));
        $activeMonth = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $rawMonth) ? $rawMonth : $currentMonth;
        $monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $monthOptions = [];
        $monthCursor = \Carbon\Carbon::createFromFormat('Y-m', $currentMonth)->startOfMonth();
        for ($i = 0; $i < 24; $i++) {
            $monthOptions[$monthCursor->format('Y-m')] = $monthNames[(int) $monthCursor->format('n')] . ' ' . $monthCursor->format('Y');
            $monthCursor->subMonth();
        }
        if (!isset($monthOptions[$activeMonth])) {
            $monthOptions[$activeMonth] = $monthNames[(int) substr($activeMonth, 5, 2)] . ' ' . substr($activeMonth, 0, 4);
        }
        $page = (int) ($meta['page'] ?? 1);
        $lastPage = (int) ($meta['last_page'] ?? 1);
        $pageStart = max(1, min($page - 2, $lastPage - 4));
        $pageEnd = min($lastPage, $pageStart + 4);
        $from = http_build_query(array_filter([
            'tab'     => $activeTab,
            'search'  => $filters['search'],
            'printed' => $onlyUnprinted ? '0' : null,
            'month'   => $activeTab === 'HISTORY' ? $activeMonth : null,
            'page'    => ($meta['page'] ?? 1) > 1 ? $meta['page'] : null,
        ], function ($value) {
            return $value !== null && $value !== '';
        }));
        $detailUrl = function ($id) use ($from) {
            return route('delivery_order.show', ['id' => $id, 'from' => $from]);
        };
        $printUrl = function ($id) use ($from) {
            return route('delivery_order.print_spk', ['id' => $id, 'from' => $from]);
        };
        $link = function (array $overrides = []) {
            $query = array_merge(request()->query(), $overrides);
            $query = array_filter($query, function ($value) {
                return $value !== null && $value !== '';
            });
            return route('delivery_order', $query);
        };
    @endphp

    <div class="dhead">
        <div>
            <h1>Checker Transaksi (DO)</h1>
            <p>Pemrosesan DO gudang — {{ $onlyUnprinted ? 'DO belum dicetak SPK.' : ($hints[$activeTab] ?? '') }}</p>
        </div>
        <div class="dhead-side">
            @if($activeTab === 'HISTORY')
                <form method="GET" action="{{ route('delivery_order') }}" class="month-form">
                    <input type="hidden" name="tab" value="HISTORY">
                    @if($filters['search'] !== '')
                        <input type="hidden" name="search" value="{{ $filters['search'] }}">
                    @endif
                    <label class="month-pick">
                        <span>Bulan</span>
                        <select name="month" onchange="this.form.submit()" aria-label="Pilih bulan">
                            @foreach($monthOptions as $value => $label)
                                <option value="{{ $value }}" {{ $value === $activeMonth ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                </form>
            @endif
            @if($updated)
                <span>Diperbarui {{ $updated }} WIB</span>
            @endif
            <a href="{{ $link() }}" class="icon-btn" title="Muat ulang" aria-label="Muat ulang">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 3v6h-6"/></svg>
            </a>
        </div>
    </div>

    <div class="toolbar">
        <nav class="tabs">
            @foreach($labels as $tab => $label)
                <a href="{{ $link(['tab' => $tab, 'printed' => null, 'page' => null]) }}" class="tab {{ $activeTab === $tab ? 'active' : '' }}">
                    {{ $label }}
                    <span class="n">{{ (int) ($tabs[$tab]['count'] ?? 0) }}</span>
                </a>
            @endforeach
        </nav>

        <form method="GET" action="{{ route('delivery_order') }}" class="search-inline">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            @if($onlyUnprinted)
                <input type="hidden" name="printed" value="0">
            @endif
            @if($activeTab === 'HISTORY')
                <input type="hidden" name="month" value="{{ $activeMonth }}">
            @endif
            <input type="text" id="search" name="search" value="{{ $filters['search'] }}" placeholder="Cari DO, SO, customer" aria-label="Cari">
            <button type="submit" class="btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                Cari
            </button>
            @if($filters['search'] !== '' || $onlyUnprinted)
                <a href="{{ route('delivery_order', $activeTab === 'HISTORY' ? ['tab' => 'HISTORY', 'month' => $activeMonth] : ['tab' => $activeTab]) }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    @if($onlyUnprinted)
        <div class="error-note" style="background:#f0fdfa;border-color:#b8e6d8;color:#0f766e;">
            <span>Menampilkan hanya DO yang belum dicetak SPK.</span>
            <a href="{{ $link(['printed' => null, 'page' => null]) }}" style="background:#0f766e;">Tampilkan semua List SPK</a>
        </div>
    @endif

    @if(!$data)
        <div class="error-note">
            <span>{{ $errorMessage ?? 'Daftar DO belum bisa dimuat.' }}</span>
            <a href="{{ route('delivery_order') }}">Coba lagi</a>
        </div>
    @else
        <section class="panel" style="{{ $onlyUnprinted ? 'margin-top:12px;' : '' }}">
            <header class="panel-head">
                <div>
                    <h2>{{ $labels[$activeTab] ?? $activeTab }}</h2>
                    <small>{{ $onlyUnprinted ? 'DO belum dicetak SPK' : ($hints[$activeTab] ?? '') }}</small>
                </div>
                <span class="count-pill">{{ $meta['total'] }} dokumen</span>
            </header>

            @if(empty($rows))
                <div class="empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.7l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.7l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/><path d="M3.3 7L12 12l8.7-5"/><path d="M12 22V12"/></svg>
                    <span>
                        <b>Tidak ada DO pada daftar ini</b>
                        <small>{{ $hints[$activeTab] ?? 'Coba pilih tab lain.' }}</small>
                    </span>
                </div>
            @else
                <div class="table-wrap">
                    <table class="do-table">
                        <thead>
                            <tr>
                                <th style="width: 40px;">No</th>
                                <th>DO</th>
                                <th class="col-so">SO</th>
                                <th>Customer</th>
                                <th class="col-created">Dibuat</th>
                                @if($activeTab === 'HISTORY')
                                    <th>Tgl Kirim</th>
                                @endif
                                <th>Status</th>
                                <th>Keterangan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td class="cell-muted">{{ $row['no'] }}</td>
                                    <td class="cell-code">{{ $row['do_code'] }}</td>
                                    <td class="cell-muted col-so">{{ $row['so_code'] }}</td>
                                    <td class="cell-muted">{{ $row['customer'] }}</td>
                                    <td class="cell-date col-created">{{ $row['created_at'] }}</td>
                                    @if($activeTab === 'HISTORY')
                                        <td class="cell-date">{{ $row['date_sent'] }}</td>
                                    @endif
                                    <td>
                                        <span class="badge-status {{ $statusBadge[$row['status']] ?? 'st-neutral' }}">{{ $row['status'] }}</span>
                                    </td>
                                    <td class="cell-muted">
                                        @if($row['print_count'] === 0)
                                            <span class="tag" style="background:#fef3c7;color:#92400e;margin-left:0;">Belum dicetak</span>
                                        @endif
                                        @if($row['kurs_hold'])
                                            <span class="tag" style="background:#fffbeb;color:#b45309;margin-left:0;">Kurs belum di-set</span>
                                        @endif
                                        @if($row['void_status'])
                                            <span class="tag" style="margin-left:0;">Pengajuan void</span>
                                        @endif
                                        @if($row['cash'])
                                            <span class="tag" style="background:#eff6ff;color:#1d4ed8;margin-left:0;">CASH</span>
                                        @endif
                                        @if($row['print_count'] > 0 && !$row['kurs_hold'] && !$row['void_status'] && !$row['cash'])
                                            -
                                        @endif
                                    </td>
                                    <td class="cell-actions">
                                        @if($row['status'] === 'READY')
                                            @if($row['print_count'] === 0)
                                                <a href="{{ $printUrl($row['id']) }}" target="_blank" class="mini-btn primary">
                                                    Print SPK
                                                </a>
                                                <button type="button" class="mini-btn ghost" disabled title="Print SPK dulu sebelum dikerjakan">
                                                    Kerjakan
                                                </button>
                                            @else
                                                <a href="{{ $printUrl($row['id']) }}" target="_blank" class="mini-btn ghost" title="Print ulang SPK">
                                                    Print
                                                </a>
                                                <a href="{{ $detailUrl($row['id']) }}" class="mini-btn primary">
                                                    Kerjakan
                                                </a>
                                            @endif
                                        @elseif($row['status'] === 'PACKED')
                                            @if($row['kurs_hold'])
                                                <button type="button" class="mini-btn ghost" disabled title="Kurs IDR belum di-set">
                                                    Kurs belum di-set
                                                </button>
                                            @else
                                                <a href="{{ $detailUrl($row['id']) }}" class="mini-btn primary">
                                                    Surat Jalan
                                                </a>
                                            @endif
                                        @elseif($row['status'] === 'DELIVERING')
                                            @if($row['void_status'])
                                                <button type="button" class="mini-btn ghost" disabled title="Sedang pengajuan void">
                                                    Pengajuan void
                                                </button>
                                            @elseif($row['cash'] && !$row['has_payment'])
                                                <button type="button" class="mini-btn ghost" disabled title="Pembayaran CASH belum tercatat">
                                                    CASH belum dibayar
                                                </button>
                                            @else
                                                <a href="{{ $detailUrl($row['id']) }}" class="mini-btn primary">
                                                    Update Resi
                                                </a>
                                            @endif
                                        @else
                                            <a href="{{ $detailUrl($row['id']) }}" class="mini-btn ghost">
                                                Lihat Detail
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($meta['last_page'] > 1)
                    <div class="pager">
                        <span>Halaman {{ $meta['page'] }} dari {{ $meta['last_page'] }} · {{ $meta['total'] }} dokumen</span>
                        <div class="links">
                            @if($page > 1)
                                <a href="{{ $link(['page' => $page - 1]) }}" title="Sebelumnya" aria-label="Sebelumnya">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
                                    <span class="pg-text">Sebelumnya</span>
                                </a>
                            @else
                                <span class="disabled" aria-disabled="true">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
                                    <span class="pg-text">Sebelumnya</span>
                                </span>
                            @endif

                            @if($pageStart > 1)
                                <a href="{{ $link(['page' => 1]) }}">1</a>
                                @if($pageStart > 2)
                                    <span class="pg-gap">…</span>
                                @endif
                            @endif

                            @for($p = $pageStart; $p <= $pageEnd; $p++)
                                @if($p === $page)
                                    <span class="current">{{ $p }}</span>
                                @else
                                    <a href="{{ $link(['page' => $p]) }}">{{ $p }}</a>
                                @endif
                            @endfor

                            @if($pageEnd < $lastPage)
                                @if($pageEnd < $lastPage - 1)
                                    <span class="pg-gap">…</span>
                                @endif
                                <a href="{{ $link(['page' => $lastPage]) }}">{{ $lastPage }}</a>
                            @endif

                            @if($page < $lastPage)
                                <a href="{{ $link(['page' => $page + 1]) }}" title="Berikutnya" aria-label="Berikutnya">
                                    <span class="pg-text">Berikutnya</span>
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
                                </a>
                            @else
                                <span class="disabled" aria-disabled="true">
                                    <span class="pg-text">Berikutnya</span>
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </section>
    @endif
@endsection
