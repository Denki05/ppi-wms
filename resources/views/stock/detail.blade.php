@extends('layouts.app')

@section('title', 'Kartu Stock')

@push('styles')
@include('partials.list-styles')
@include('partials.detail-styles')
@if($embed)
<style>
    .topbar { display: none; }
    main { max-width: 100%; padding: 16px 16px 24px; }
    body { background: #fff; }
    .back-link { display: none; }
</style>
@endif
@endpush

@section('content')
    @php
        $product = $data['product'] ?? [];
        $warehouse = $data['warehouse'] ?? [];
        $rows = $data['rows'] ?? [];
        $monthValue = $data['month'] ?? ($month !== '' ? $month : now()->format('Y-m'));
        $opening = (float) ($data['opening_balance'] ?? 0);
        $real = (float) ($data['real_balance'] ?? 0);
        $totalIn = 0;
        $totalOut = 0;
        foreach ($rows as $row) {
            $totalIn += (float) ($row['in'] ?? 0);
            $totalOut += (float) ($row['out'] ?? 0);
        }
        $lastBalance = !empty($rows) ? (float) end($rows)['balance'] : $opening;
        $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
        $monthParts = explode('-', $monthValue);
        $monthLabel = (isset($bulan[(int) ($monthParts[1] ?? 0)]) ? $bulan[(int) $monthParts[1]] : $monthValue) . ' ' . ($monthParts[0] ?? '');
        $num = function ($value) {
            return number_format((float) $value, 2);
        };
    @endphp

    <a href="{{ $backUrl }}" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
        Kembali ke daftar Stock
    </a>

    @if(session('error'))
        <div class="notice err">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><path d="M12 16h.01"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(!$data)
        <div class="error-note">
            <span>{{ $errorMessage ?? 'Kartu stock belum bisa dimuat.' }}</span>
            <a href="{{ $backUrl }}">Kembali ke daftar</a>
        </div>
    @else
        <section class="hero">
            <div class="hero-top">
                <div>
                    <h1>{{ $product['code'] ?? '-' }}</h1>
                    <p>{{ $product['name'] ?? '-' }} / {{ $product['packaging'] ?? '-' }} · {{ $warehouse['name'] ?? '-' }}</p>
                </div>
                <div class="hero-chips">
                    <span class="chip">Kartu Stock</span>
                    <span class="chip">{{ $monthLabel }}</span>
                </div>
            </div>

            <div class="meta-grid">
                <div class="meta-item">
                    <span>Produk</span>
                    <b>{{ $product['name'] ?? '-' }}</b>
                </div>
                <div class="meta-item">
                    <span>Brand</span>
                    <b>{{ $product['brand'] ?? '-' }}</b>
                </div>
                <div class="meta-item">
                    <span>Kemasan</span>
                    <b>{{ $product['packaging'] ?? '-' }}</b>
                </div>
                <div class="meta-item">
                    <span>Gudang</span>
                    <b>{{ $warehouse['name'] ?? '-' }}</b>
                </div>
            </div>
        </section>

        <div class="stat-grid">
            <div class="stat" style="--accent: #94a3b8;">
                <span>Saldo Awal</span>
                <b>{{ $num($opening) }}</b>
            </div>
            <div class="stat" style="--accent: #16a34a;">
                <span>Total Masuk</span>
                <b>{{ $num($totalIn) }}</b>
            </div>
            <div class="stat" style="--accent: #dc2626;">
                <span>Total Keluar</span>
                <b>{{ $num($totalOut) }}</b>
            </div>
            <div class="stat" style="--accent: #0f766e;">
                <span>Saldo Kartu</span>
                <b>{{ $num($lastBalance) }}</b>
            </div>
            <div class="stat" style="--accent: #6d28d9;">
                <span>Stock Tercatat</span>
                <b>{{ $num($real) }}</b>
            </div>
        </div>

        <div class="action-bar">
            <form method="GET" action="{{ route('stock.detail', ['warehouseId' => $warehouseId, 'encoded' => $encoded]) }}" class="action-buttons">
                @if(request()->query('from'))
                    <input type="hidden" name="from" value="{{ request()->query('from') }}">
                @endif
                @if($embed)
                    <input type="hidden" name="embed" value="1">
                @endif
                <div class="field">
                    <label for="month">Periode</label>
                    <input type="month" id="month" name="month" value="{{ $monthValue }}">
                </div>
                <button type="submit" class="btn">Tampilkan</button>
            </form>

            <div class="action-buttons">
                <a class="btn-line" target="_blank" rel="noopener"
                   href="{{ route('stock.pdf', ['warehouseId' => $warehouseId, 'encoded' => $encoded, 'month' => $monthValue]) }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><path d="M6 18H4a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                    Cetak PDF
                </a>
            </div>
        </div>

        <section class="panel detail-panel">
            <header class="panel-head">
                <div>
                    <h2>Pergerakan Stock</h2>
                    <small>Masuk, keluar, dan saldo berjalan pada periode ini</small>
                </div>
                <span class="count-pill">{{ count($rows) }} baris</span>
            </header>

            @if(empty($rows))
                <div class="empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/></svg>
                    <span><b>Tidak ada pergerakan</b><small>Tidak ada transaksi pada periode ini.</small></span>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 130px;">Tanggal</th>
                                <th>Transaksi</th>
                                <th class="num" style="width: 100px;">Masuk</th>
                                <th class="num" style="width: 100px;">Keluar</th>
                                <th class="num" style="width: 110px;">Saldo</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td class="cell-date">{{ $row['date'] }}</td>
                                    <td class="cell-code">{{ $row['transaction'] }}</td>
                                    <td class="num {{ !empty($row['in']) ? 'qc-ok' : 'cell-muted' }}">
                                        {{ !empty($row['in']) ? $num($row['in']) : '-' }}
                                    </td>
                                    <td class="num {{ !empty($row['out']) ? 'qc-bad' : 'cell-muted' }}">
                                        {{ !empty($row['out']) ? $num($row['out']) : '-' }}
                                    </td>
                                    <td class="num {{ (float) $row['balance'] < 0 ? 'negative' : '' }}">{{ $num($row['balance']) }}</td>
                                    <td class="cell-muted">{{ $row['description'] ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif
@endsection
