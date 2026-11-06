@extends('layouts.app')

@section('title', 'Checker Mutasi')

@push('styles')
@include('partials.list-styles')
<style>
    .mutasi-toolbar {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }
    .mutasi-toolbar .seg a { white-space: nowrap; }
    .toolbar-sep {
        flex: none;
        width: 1px;
        align-self: stretch;
        min-height: 22px;
        margin: 0 3px;
        background: #e3ece8;
    }
    .mutasi-search {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 0 1 360px;
        margin-left: auto;
        min-width: 0;
    }
    .mutasi-search input[type="text"] {
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
    .mutasi-search input[type="text"]:focus { border-color: #14b8a6; box-shadow: 0 0 0 3px rgba(20,184,166,.12); }
    .mutasi-search .btn { min-height: 34px; padding: 7px 12px; }
    .mutasi-search .btn-reset { min-height: 34px; }

    @media (max-width: 1199px) {
        .mutasi-search { flex: 0 1 280px; }
    }
    @media (max-width: 991px) {
        .mutasi-search { flex: 1 1 100%; margin-left: 0; }
    }
    @media (max-width: 620px) {
        .mutasi-toolbar { gap: 8px; }
        .toolbar-sep { display: none; }
        .mutasi-toolbar .seg { width: 100%; }
        .mutasi-toolbar .seg a { flex: 1 1 0; text-align: center; }
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
        $activeType = $data['type'] ?? ($filters['type'] ?? 'showroom');
        $activeTab = $data['tab'] ?? ($filters['tab'] ?? 'AKTIF');
        $labels = ['AKTIF' => 'Aktif', 'BELUM' => 'Belum Diambil', 'SELESAI' => 'Selesai'];
        $hints = [
            'AKTIF'   => 'Mutasi sudah dipublish, menunggu pengecekan',
            'BELUM'   => 'Barang belum diambil',
            'SELESAI' => 'Barang sudah diambil',
        ];
        $barangBadge = ['Pending' => 'st-neutral', 'Belum Diambil' => 'st-active', 'Diambil' => 'st-acc'];
        $statusBadge = ['Aktif' => 'st-active', 'Publish' => 'st-publish', 'ACC' => 'st-acc', 'Draft' => 'st-neutral'];
        $fromQuery = request()->getQueryString();
        $link = function (array $overrides = []) {
            $query = array_merge(request()->query(), $overrides);
            $query = array_filter($query, function ($value) {
                return $value !== null && $value !== '';
            });
            return route('mutasi', $query);
        };
    @endphp

    <div class="dhead">
        <div>
            <h1>Checker Mutasi</h1>
            <p>Pantau perpindahan barang antar gudang dan showroom — {{ $hints[$activeTab] ?? '' }}.</p>
        </div>
        <div class="dhead-side">
            @if($updated)
                <span>Diperbarui {{ $updated }} WIB</span>
            @endif
            <a href="{{ $link() }}" class="icon-btn" title="Muat ulang" aria-label="Muat ulang">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 3v6h-6"/></svg>
            </a>
        </div>
    </div>

    <div class="toolbar mutasi-toolbar">
        <div class="seg">
            <a href="{{ $link(['type' => 'showroom', 'tab' => 'AKTIF', 'page' => null]) }}" class="{{ $activeType === 'showroom' ? 'active' : '' }}">Mutasi Showroom</a>
            <a href="{{ $link(['type' => 'gudang', 'tab' => 'AKTIF', 'page' => null]) }}" class="{{ $activeType === 'gudang' ? 'active' : '' }}">Mutasi Gudang Utama</a>
        </div>

        <span class="toolbar-sep" aria-hidden="true"></span>

        <nav class="tabs">
            @foreach($labels as $tab => $label)
                <a href="{{ $link(['tab' => $tab, 'page' => null]) }}" class="tab {{ $activeTab === $tab ? 'active' : '' }}">
                    {{ $label }}
                    <span class="n">{{ (int) ($tabs[$tab]['count'] ?? 0) }}</span>
                </a>
            @endforeach
        </nav>

        <form method="GET" action="{{ route('mutasi') }}" class="mutasi-search">
            <input type="hidden" name="type" value="{{ $activeType }}">
            <input type="hidden" name="tab" value="{{ $activeTab }}">
            <input type="text" id="search" name="search" value="{{ $filters['search'] }}" placeholder="{{ $activeType === 'showroom' ? 'Kode mutasi, SO, atau customer' : 'Kode mutasi, gudang, atau catatan' }}" aria-label="Cari">
            <button type="submit" class="btn">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
                Cari
            </button>
            @if($filters['search'] !== '')
                <a href="{{ route('mutasi', ['type' => $activeType, 'tab' => $activeTab]) }}" class="btn-reset">Reset</a>
            @endif
        </form>
    </div>

    @if(!$data)
        <div class="error-note">
            <span>{{ $errorMessage ?? 'Daftar mutasi belum bisa dimuat.' }}</span>
            <a href="{{ route('mutasi') }}">Coba lagi</a>
        </div>
    @else
        <section class="panel">
            <header class="panel-head">
                <div>
                    <h2>{{ $activeType === 'showroom' ? 'Mutasi Showroom' : 'Mutasi Gudang Utama' }}</h2>
                    <small>{{ $labels[$activeTab] ?? $activeTab }} · {{ $hints[$activeTab] ?? '' }}</small>
                </div>
                <span class="count-pill">{{ $meta['total'] }} dokumen</span>
            </header>

            @if(empty($rows))
                <div class="empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7 16V4"/><path d="M3 8l4-4 4 4"/><path d="M17 8v12"/><path d="M21 16l-4 4-4-4"/></svg>
                    <span>
                        <b>Tidak ada mutasi pada tab ini</b>
                        <small>{{ $hints[$activeTab] ?? 'Coba pilih tab lain.' }}</small>
                    </span>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        @if($activeType === 'showroom')
                            <thead>
                                <tr>
                                    <th style="width: 44px;">No</th>
                                    <th>Tanggal</th>
                                    <th>Kode</th>
                                    <th>Customer</th>
                                    <th>Tipe</th>
                                    <th>Status</th>
                                    <th>Status Barang</th>
                                    <th style="width: 70px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $row)
                                    <tr>
                                        <td class="cell-muted">{{ $row['no'] }}</td>
                                        <td class="cell-date">{{ $row['tanggal'] }}</td>
                                        <td>
                                            <span class="cell-code">{{ $row['code'] }}</span>
                                            @if($row['so'] !== '-')
                                                <span class="cell-name">SO {{ $row['so'] }}</span>
                                            @endif
                                        </td>
                                        <td class="cell-muted">{{ $row['customer'] }}</td>
                                        <td class="cell-muted">{{ $row['type'] }}</td>
                                        <td><span class="badge-status {{ $statusBadge[$row['status']] ?? 'st-neutral' }}">{{ $row['status'] }}</span></td>
                                        <td><span class="badge-status {{ $barangBadge[$row['barang']] ?? 'st-neutral' }}">{{ $row['barang'] }}</span></td>
                                        <td>
                                            <a href="{{ route('mutasi.show', ['type' => 'showroom', 'id' => $row['id'], 'from' => $fromQuery]) }}"
                                               class="mini-btn ghost">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @else
                            <thead>
                                <tr>
                                    <th style="width: 44px;">No</th>
                                    <th>Tanggal</th>
                                    <th>Kode</th>
                                    <th>Gudang Asal → Tujuan</th>
                                    <th>Status</th>
                                    <th>Status Barang</th>
                                    <th style="width: 70px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rows as $row)
                                    <tr>
                                        <td class="cell-muted">{{ $row['no'] }}</td>
                                        <td class="cell-date">{{ $row['tanggal'] }}</td>
                                        <td class="cell-code">{{ $row['code'] }}</td>
                                        <td class="cell-muted">{{ $row['customer'] }}</td>
                                        <td><span class="badge-status {{ $statusBadge[$row['status']] ?? 'st-neutral' }}">{{ $row['status'] }}</span></td>
                                        <td><span class="badge-status {{ $barangBadge[$row['barang']] ?? 'st-neutral' }}">{{ $row['barang'] }}</span></td>
                                        <td>
                                            <a href="{{ route('mutasi.show', ['type' => 'gudang', 'id' => $row['id'], 'from' => $fromQuery]) }}"
                                               class="mini-btn ghost">Detail</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        @endif
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
