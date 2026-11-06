@extends('layouts.app')

@section('title', 'Detail Receiving - Komplain')

@push('styles')
@include('partials.list-styles')
@include('partials.detail-styles')
@endpush

@section('content')
    @php
        $header = $data['header'] ?? [];
        $stats = $data['stats'] ?? [];
        $rows = $data['rows'] ?? [];
        $badge = ['ACTIVE' => 'st-active', 'ACC' => 'st-acc'];
        $num = function ($value) {
            $text = number_format((float) $value, 2, ',', '.');
            return rtrim(rtrim($text, '0'), ',');
        };
        $updated = !empty($data['generated_at'])
            ? \Carbon\Carbon::parse($data['generated_at'])->setTimezone('Asia/Jakarta')->format('H:i')
            : null;
        $status = $header['status'] ?? '';
    @endphp

    <a href="{{ $backUrl }}" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
        Kembali ke daftar Receiving - Komplain
    </a>

    @if(session('ok'))
        <div class="notice ok" data-flash="ok">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
            <span>{{ session('ok') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="notice err" data-flash="err">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><path d="M12 16h.01"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(!$data)
        <div class="error-note">
            <span>{{ $errorMessage ?? 'Detail komplain belum bisa dimuat.' }}</span>
            <a href="{{ $backUrl }}">Kembali ke daftar</a>
        </div>
    @else
        <section class="hero">
            <div class="hero-top">
                <div>
                    <h1>{{ $header['code'] }}</h1>
                    <p>Penerimaan komplain ke gudang QC · {{ $header['warehouse'] }}</p>
                </div>
                <div class="hero-chips">
                    <span class="badge-status {{ $badge[$status] ?? 'st-neutral' }}">{{ $status }}</span>
                    @if($updated)
                        <span class="chip">Diperbarui {{ $updated }} WIB</span>
                    @endif
                </div>
            </div>

            <div class="meta-grid">
                <div class="meta-item">
                    <span>Customer</span>
                    <b>{{ $header['customer'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Gudang Tujuan</span>
                    <b>{{ $header['warehouse'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Tanggal</span>
                    <b>{{ $header['tanggal'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Dibuat</span>
                    <b>{{ $header['created_at'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Dibuat oleh</span>
                    <b>{{ $header['created_by'] }}</b>
                </div>
                @if(!empty($header['retur']))
                    <div class="meta-item">
                        <span>Retur</span>
                        <b>{{ $header['retur'] }}</b>
                    </div>
                @endif
                <div class="meta-item">
                    <span>Catatan</span>
                    <b>{{ $header['note'] !== '' ? $header['note'] : '-' }}</b>
                </div>
            </div>
        </section>

        <div class="stat-grid">
            <div class="stat" style="--accent: #0f766e;">
                <span>Jumlah Produk</span>
                <b>{{ (int) ($stats['items'] ?? 0) }}</b><small>baris</small>
            </div>
            <div class="stat" style="--accent: #8b5cf6;">
                <span>Total Qty</span>
                <b>{{ $num($stats['total_qty'] ?? 0) }}</b>
            </div>
        </div>

        <div class="action-bar">
            @if(!empty($data['can_acc']))
                <div class="note">
                    <b>Siap di-ACC.</b> Barang akan masuk ke Gudang Araya lalu ditransfer ke {{ $header['warehouse'] }}. Stok berubah dan tindakan ini tidak bisa dibatalkan.
                </div>
                <form method="POST" action="{{ route('komplain.acc', ['id' => $header['id']]) }}"
                      data-confirm="ACC komplain {{ $header['code'] }}? Stok akan dipindahkan ke gudang QC. Tindakan ini tidak bisa dibatalkan.">
                    @csrf
                    <input type="hidden" name="from" value="{{ (string) request()->query('from', '') }}">
                    <button type="submit" class="btn-acc">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                        ACC Komplain
                    </button>
                </form>
            @elseif(!empty($data['can_pdf']))
                <div class="note"><b>Sudah di-ACC.</b> Surat jalan komplain bisa dicetak ulang kapan saja.</div>
                <a href="{{ route('komplain.pdf', ['id' => $header['id']]) }}" target="_blank" class="btn-line">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                    Surat Jalan
                </a>
            @elseif(!empty($data['acc_blocked']))
                <div class="note warn">{{ $data['acc_blocked'] }}</div>
            @endif
        </div>

        <section class="panel detail-panel">
            <header class="panel-head">
                <div>
                    <h2>Daftar Produk</h2>
                    <small>{{ count($rows) }} baris · qty komplain yang diterima</small>
                </div>
                <span class="count-pill">{{ $header['code'] }}</span>
            </header>

            @if(empty($rows))
                <div class="empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/></svg>
                    <span>
                        <b>Belum ada produk</b>
                        <small>Komplain ini belum memiliki baris produk.</small>
                    </span>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 44px;">No</th>
                                <th>Produk</th>
                                <th>Kemasan</th>
                                <th class="num">Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td class="cell-muted">{{ $row['no'] }}</td>
                                    <td class="cell-code">{{ $row['product'] }}</td>
                                    <td class="cell-muted">{{ $row['pack'] }}</td>
                                    <td class="num">{{ $num($row['qty']) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif
@endsection
