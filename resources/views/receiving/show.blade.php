@extends('layouts.app')

@section('title', 'Detail Receiving')

@push('styles')
@include('partials.list-styles')
@include('partials.detail-styles')
@endpush

@section('content')
    @php
        $header = $data['header'] ?? [];
        $stats = $data['stats'] ?? [];
        $rows = $data['rows'] ?? [];
        $badge = ['ACTIVE' => 'st-active', 'QC' => 'st-qc', 'READY' => 'st-ready', 'ACC' => 'st-acc'];
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
        Kembali ke daftar Receiving
    </a>

    @if(session('ok'))
        <div class="notice ok">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
            <span>{{ session('ok') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="notice err">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 8v5"/><path d="M12 16h.01"/></svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(!$data)
        <div class="error-note">
            <span>{{ $errorMessage ?? 'Detail penerimaan belum bisa dimuat.' }}</span>
            <a href="{{ $backUrl }}">Kembali ke daftar</a>
        </div>
    @else
        <section class="hero">
            <div class="hero-top">
                <div>
                    <h1>{{ $header['code'] }}</h1>
                    <p>Penerimaan barang masuk · {{ $header['warehouse'] }}</p>
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
                    <span>Gudang</span>
                    <b>{{ $header['warehouse'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Tanggal PBM</span>
                    <b>{{ $header['pbm_date'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Dibuat</span>
                    <b>{{ $header['created_at'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Dibuat oleh</span>
                    <b>{{ $header['created_by'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Catatan</span>
                    <b>{{ $header['note'] !== '' ? $header['note'] : '-' }}</b>
                </div>
                @if(!empty($header['acc_at']))
                    <div class="meta-item">
                        <span>Di-ACC oleh</span>
                        <b>{{ $header['acc_by'] }} · {{ $header['acc_at'] }}</b>
                    </div>
                @endif
            </div>
        </section>

        <div class="stat-grid">
            <div class="stat" style="--accent: #0f766e;">
                <span>Produk</span>
                <b>{{ (int) ($stats['items'] ?? 0) }}</b><small>baris</small>
            </div>
            <div class="stat" style="--accent: #3b82f6;">
                <span>Qty PO</span>
                <b>{{ $num($stats['qty_po'] ?? 0) }}</b>
            </div>
            <div class="stat" style="--accent: #8b5cf6;">
                <span>Qty Diterima</span>
                <b>{{ $num($stats['qty_ri'] ?? 0) }}</b>
            </div>
            <div class="stat" style="--accent: {{ (float) ($stats['selisih'] ?? 0) != 0 ? '#dc2626' : '#94a3b8' }};">
                <span>Selisih</span>
                <b>{{ $num($stats['selisih'] ?? 0) }}</b>
            </div>
            <div class="stat" style="--accent: #16a34a;">
                <span>QC OK</span>
                <b>{{ $num($stats['qc_ok'] ?? 0) }}</b>
            </div>
            <div class="stat" style="--accent: #dc2626;">
                <span>QC NOT OK</span>
                <b>{{ $num($stats['qc_not_ok'] ?? 0) }}</b>
            </div>
            <div class="stat" style="--accent: #f59e0b;">
                <span>QC HOLD</span>
                <b>{{ $num($stats['qc_hold'] ?? 0) }}</b>
            </div>
            <div class="stat" style="--accent: #0891b2;">
                <span>QC Disetujui</span>
                <b>{{ $num($stats['qc_approved'] ?? 0) }}</b>
            </div>
        </div>

        @php
            $fromQuery = (string) request()->query('from', '');
        @endphp

        <div class="action-bar">
            <div>
                @if($status === 'ACTIVE')
                    <div class="note"><b>Draft penerimaan.</b> Publish ke QC setelah daftar produk siap diperiksa.</div>
                @elseif($status === 'QC')
                    <div class="note"><b>Proses QC.</b> Publish to Ready bila seluruh hasil QC sudah valid dan di-approve.</div>
                @elseif($status === 'READY' && !empty($data['can_acc']))
                    <div class="note">
                        <b>Siap di-ACC.</b> Stok akan masuk ke {{ $header['warehouse'] }} dan pemakaian PO dipotong sesuai qty QC. Tindakan ini tidak bisa dibatalkan.
                    </div>
                @elseif($status === 'READY' && !empty($data['acc_blocked']))
                    <div class="note warn">{{ $data['acc_blocked'] }}</div>
                @else
                    <div class="note">Penerimaan sudah final dan tidak bisa diubah lagi.</div>
                @endif
            </div>

            <div class="action-buttons">
                @if($status === 'ACTIVE' && !empty($data['can_publish']))
                    <form method="POST" action="{{ route('receiving.publish', ['id' => $header['id']]) }}"
                          onsubmit="if (!confirm('Publish penerimaan {{ $header['code'] }} ke tahap QC?')) { return false; } this.querySelector('button').disabled = true; return true;">
                        @csrf
                        <input type="hidden" name="from" value="{{ $fromQuery }}">
                        <button type="submit" class="btn-acc">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                            Publish ke QC
                        </button>
                    </form>
                @endif

                @if($status === 'QC' && !empty($data['can_publish']))
                    <form method="POST" action="{{ route('receiving.publish', ['id' => $header['id']]) }}"
                          onsubmit="if (!confirm('Publish penerimaan {{ $header['code'] }} ke tahap Ready?')) { return false; } this.querySelector('button').disabled = true; return true;">
                        @csrf
                        <input type="hidden" name="from" value="{{ $fromQuery }}">
                        <button type="submit" class="btn-acc">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                            Publish to Ready
                        </button>
                    </form>
                @endif

                @if($status === 'QC' && !empty($data['can_cancel']))
                    <form method="POST" action="{{ route('receiving.cancel', ['id' => $header['id']]) }}"
                          onsubmit="if (!confirm('Kembalikan penerimaan {{ $header['code'] }} ke ACTIVE? Ditolak bila sudah ada log QC.')) { return false; } this.querySelector('button').disabled = true; return true;">
                        @csrf
                        <input type="hidden" name="from" value="{{ $fromQuery }}">
                        <button type="submit" class="btn-line">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                            Kembalikan ke Active
                        </button>
                    </form>
                @endif

                @if($status === 'READY' && !empty($data['can_rollback']))
                    <form method="POST" action="{{ route('receiving.rollback', ['id' => $header['id']]) }}"
                          onsubmit="if (!confirm('Kembalikan penerimaan {{ $header['code'] }} ke tahap QC untuk revisi?')) { return false; } this.querySelector('button').disabled = true; return true;">
                        @csrf
                        <input type="hidden" name="from" value="{{ $fromQuery }}">
                        <button type="submit" class="btn-line">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>
                            Kembalikan ke QC
                        </button>
                    </form>
                @endif

                @if(!empty($data['can_acc']))
                    <form method="POST" action="{{ route('receiving.acc', ['id' => $header['id']]) }}"
                          onsubmit="if (!confirm('ACC penerimaan {{ $header['code'] }}? Stok akan ditambahkan dan PO dipotong. Tindakan ini tidak bisa dibatalkan.')) { return false; } this.querySelector('button').disabled = true; this.querySelector('button').textContent = 'Memproses...'; return true;">
                        @csrf
                        <input type="hidden" name="from" value="{{ $fromQuery }}">
                        <button type="submit" class="btn-acc">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                            ACC Penerimaan
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <section class="panel detail-panel">
            <header class="panel-head">
                <div>
                    <h2>Daftar Produk</h2>
                    <small>{{ count($rows) }} baris · qty PO, qty diterima, dan hasil QC</small>
                </div>
                <span class="count-pill">{{ $header['code'] }}</span>
            </header>

            @if(empty($rows))
                <div class="empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/></svg>
                    <span>
                        <b>Belum ada produk</b>
                        <small>Penerimaan ini belum memiliki baris produk.</small>
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
                                <th class="num">Qty PO</th>
                                <th class="num">Qty Diterima</th>
                                <th class="num">Selisih</th>
                                <th class="num">QC OK</th>
                                <th class="num">QC NOT OK</th>
                                <th class="num">QC HOLD</th>
                                <th class="num">QC Disetujui</th>
                                <th>No Batch</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td class="cell-muted">{{ $row['no'] }}</td>
                                    <td class="cell-code">{{ $row['product'] }}</td>
                                    <td class="cell-muted">{{ $row['pack'] }}</td>
                                    <td class="num">{{ $num($row['qty_po']) }}</td>
                                    <td class="num">{{ $num($row['qty_ri']) }}</td>
                                    <td class="num {{ (float) $row['selisih'] != 0 ? 'negative' : 'zero' }}">{{ $num($row['selisih']) }}</td>
                                    <td class="num {{ (float) $row['qc_ok'] > 0 ? 'qc-ok' : 'zero' }}">{{ $num($row['qc_ok']) }}</td>
                                    <td class="num {{ (float) $row['qc_not_ok'] > 0 ? 'qc-bad' : 'zero' }}">{{ $num($row['qc_not_ok']) }}</td>
                                    <td class="num {{ (float) $row['qc_hold'] > 0 ? 'qc-hold' : 'zero' }}">{{ $num($row['qc_hold']) }}</td>
                                    <td class="num {{ (float) $row['qc_approved'] > 0 ? 'qc-ok' : 'zero' }}">{{ $num($row['qc_approved']) }}</td>
                                    <td class="cell-muted">{{ $row['batch'] !== '' ? $row['batch'] : '-' }}</td>
                                    <td class="cell-muted">{{ $row['note'] !== '' ? $row['note'] : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif
@endsection
