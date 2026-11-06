@extends('layouts.app')

@section('title', 'Receiving')

@push('styles')
@include('partials.list-styles')
<style>
    .receiving-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .toolbar-sep {
        flex: none;
        width: 1px;
        align-self: stretch;
        min-height: 22px;
        margin: 0 3px;
        background: #e3ece8;
    }
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
    .receiving-search {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 0 1 360px;
        margin-left: auto;
        min-width: 0;
    }
    .receiving-search input[type="text"] {
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
    .receiving-search input[type="text"]:focus { border-color: #14b8a6; box-shadow: 0 0 0 3px rgba(20,184,166,.12); }
    .receiving-search .btn { min-height: 34px; padding: 7px 12px; }
    .receiving-search .btn-reset { min-height: 34px; }

    @media (max-width: 1199px) {
        .receiving-search { flex: 0 1 280px; }
    }
    @media (max-width: 991px) {
        .receiving-search { flex: 1 1 100%; margin-left: 0; }
    }
    @media (max-width: 620px) {
        .receiving-toolbar { gap: 8px; }
        .toolbar-sep { display: none; }
        .month-pick span { display: none; }
        .dhead-side { width: 100%; justify-content: space-between; }
    }
</style>
@endpush

@section('content')
    @php
        $updated = !empty($data['generated_at'])
            ? \Carbon\Carbon::parse($data['generated_at'])->setTimezone('Asia/Jakarta')->format('H:i')
            : null;
        $tabs = collect($data['tabs'] ?? [])->keyBy('status');
        $rows = $data['rows'] ?? [];
        $meta = $data['meta'] ?? ['page' => 1, 'last_page' => 1, 'total' => 0];
        $activeStatus = $data['status'] ?? ($filters['status'] ?? 'ACTIVE');
        $labels = [
            'ACTIVE' => 'ACTIVE',
            'QC'     => 'QC',
            'READY'  => 'READY',
            'ACC'    => 'ACC',
        ];
        $hints = [
            'ACTIVE' => 'Draft penerimaan dari admin',
            'QC'     => 'Sedang diperiksa QC',
            'READY'  => 'Menunggu ACC',
            'ACC'    => 'Sudah disetujui',
        ];
        $badge = ['ACTIVE' => 'st-active', 'QC' => 'st-qc', 'READY' => 'st-ready', 'ACC' => 'st-acc'];
        $perms = $data['permissions'] ?? ['can_update' => false, 'can_approve' => false];
        $currentMonth = \Carbon\Carbon::now('Asia/Jakarta')->format('Y-m');
        $rawMonth = (string) ($data['month'] ?? ($filters['month'] ?? ''));
        $activeMonth = preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $rawMonth) ? $rawMonth : $currentMonth;
        $monthNames = [1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'];
        $monthOptions = [];
        $monthCursor = \Carbon\Carbon::createFromFormat('Y-m', $currentMonth)->startOfMonth();
        $yearStart = $monthCursor->copy()->startOfYear();
        while ($monthCursor->greaterThanOrEqualTo($yearStart)) {
            $monthOptions[$monthCursor->format('Y-m')] = $monthNames[(int) $monthCursor->format('n')] . ' ' . $monthCursor->format('Y');
            $monthCursor->subMonth();
        }
        $fromQuery = (string) request()->getQueryString();
        $link = function (array $overrides = []) {
            $query = array_merge(request()->query(), $overrides);
            $query = array_filter($query, function ($value) {
                return $value !== null && $value !== '';
            });
            return route('receiving', $query);
        };
    @endphp

    <div class="dhead">
        <div>
            <h1>Receiving</h1>
            <p>Penerimaan barang masuk — {{ $hints[$activeStatus] ?? '' }}.</p>
        </div>
        <div class="dhead-side">
            <form method="GET" action="{{ route('receiving') }}" class="month-form">
                <input type="hidden" name="status" value="{{ $activeStatus }}">
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
            @if($updated)
                <span>Diperbarui {{ $updated }} WIB</span>
            @endif
            <a href="{{ $link() }}" class="icon-btn" title="Muat ulang" aria-label="Muat ulang">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 3v6h-6"/></svg>
            </a>
        </div>
    </div>

    <div class="toolbar receiving-toolbar">
        <nav class="tabs">
            @foreach($labels as $status => $label)
                <a href="{{ $link(['status' => $status, 'page' => null]) }}" class="tab {{ $activeStatus === $status ? 'active' : '' }}">
                    {{ $label }}
                    <span class="n">{{ (int) ($tabs[$status]['count'] ?? 0) }}</span>
                </a>
            @endforeach
        </nav>

        <span class="toolbar-sep" aria-hidden="true"></span>

        <form method="GET" action="{{ route('receiving') }}" class="receiving-search">
            <input type="hidden" name="status" value="{{ $activeStatus }}">
            <input type="hidden" name="month" value="{{ $activeMonth }}">
            <input type="text" id="search" name="search" value="{{ $filters['search'] }}" placeholder="Kode penerimaan, gudang, atau catatan" aria-label="Cari">
            <button type="submit" class="btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                Cari
            </button>
            @if($filters['search'] !== '')
                <a href="{{ route('receiving', ['status' => $activeStatus, 'month' => $activeMonth]) }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    @if(!$data)
        <div class="error-note">
            <span>{{ $errorMessage ?? 'Daftar penerimaan belum bisa dimuat.' }}</span>
            <a href="{{ route('receiving') }}">Coba lagi</a>
        </div>
    @else
        <section class="panel">
            <header class="panel-head">
                <div>
                    <h2>Daftar Penerimaan</h2>
                    <small>Status {{ $labels[$activeStatus] ?? $activeStatus }} · {{ $hints[$activeStatus] ?? '' }}</small>
                </div>
                <span class="count-pill">{{ $meta['total'] }} dokumen</span>
            </header>

            @if(empty($rows))
                <div class="empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/></svg>
                    <span>
                        <b>Tidak ada dokumen pada status ini</b>
                        <small>{{ $hints[$activeStatus] ?? 'Coba pilih status lain.' }}</small>
                    </span>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 44px;">No</th>
                                <th>Kode</th>
                                <th>Tanggal PBM</th>
                                <th>Gudang</th>
                                <th>Dibuat</th>
                                <th>Catatan</th>
                                <th>Status</th>
                                <th style="width: 130px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td class="cell-muted">{{ $row['no'] }}</td>
                                    <td class="cell-code">{{ $row['code'] }}</td>
                                    <td class="cell-date">{{ $row['pbm_date'] }}</td>
                                    <td class="cell-muted">{{ $row['warehouse'] }}</td>
                                    <td class="cell-date">{{ $row['created_at'] }}</td>
                                    <td class="cell-muted">{{ $row['note'] !== '' ? $row['note'] : '-' }}</td>
                                    <td>
                                        <span class="badge-status {{ $badge[$row['status']] ?? 'st-neutral' }}">{{ $row['status'] }}</span>
                                    </td>
                                    <td>
                                        <div class="cell-actions">
                                            <a href="{{ route('receiving.show', ['id' => $row['id'], 'from' => $fromQuery]) }}"
                                               class="btn-reset" style="padding:4px 10px;min-height:0;font-size:11px;">Detail</a>
                                            @if($row['status'] === 'QC' && !empty($perms['can_update']))
                                                <form method="POST" action="{{ route('receiving.cancel', ['id' => $row['id']]) }}"
                                                      onsubmit="if (!confirm('Batalkan penerimaan {{ $row['code'] }} ke ACTIVE? Ditolak bila sudah ada log QC.')) { return false; } this.querySelector('button').disabled = true; return true;">
                                                    @csrf
                                                    <input type="hidden" name="from" value="{{ $fromQuery }}">
                                                    <button type="submit" class="mini-btn ghost" title="Kembalikan ke Active">Batal</button>
                                                </form>
                                            @endif
                                        </div>
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
                            @if($meta['page'] > 1)
                                <a href="{{ $link(['page' => $meta['page'] - 1]) }}">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
                                    Sebelumnya
                                </a>
                            @else
                                <span class="disabled">Sebelumnya</span>
                            @endif
                            @if($meta['page'] < $meta['last_page'])
                                <a href="{{ $link(['page' => $meta['page'] + 1]) }}">
                                    Berikutnya
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M9 6l6 6-6 6"/></svg>
                                </a>
                            @else
                                <span class="disabled">Berikutnya</span>
                            @endif
                        </div>
                    </div>
                @endif
            @endif
        </section>
    @endif
@endsection
