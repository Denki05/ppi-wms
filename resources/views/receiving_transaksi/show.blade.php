@extends('layouts.app')

@section('title', 'Detail Receiving - Transaksi')

@push('styles')
@include('partials.list-styles')
@include('partials.detail-styles')
<style>
    .qc-card {
        border: 1px solid #e6eeeb;
        border-radius: 12px;
        padding: 12px 14px;
        margin-bottom: 10px;
        background: #fff;
    }
    .qc-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 8px;
    }
    .qc-card-head b { color: #0f172a; }
    .qc-card-head small { color: #64748b; }
    .qc-log {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 6px 0;
        border-top: 1px dashed #e2e8f0;
        font-size: 12px;
    }
    .qc-log .qty { font-weight: 700; font-variant-numeric: tabular-nums; min-width: 60px; }
    .qc-log .status { font-weight: 700; }
    .qc-log .status.ok { color: #15803d; }
    .qc-log .status.not { color: #b91c1c; }
    .qc-form {
        display: flex;
        align-items: flex-end;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 8px;
        padding-top: 8px;
        border-top: 1px dashed #e2e8f0;
    }
    .qc-form .field { display: flex; flex-direction: column; gap: 3px; }
    .qc-form label { font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .4px; }
    .qc-form input, .qc-form select {
        border: 1px solid #d8e1dc;
        border-radius: 8px;
        padding: 7px 9px;
        font-size: 12px;
        min-height: 34px;
    }
    .qc-form input[type="number"] { width: 110px; }
    .add-detail {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        flex-wrap: wrap;
    }
    .add-detail .field { display: flex; flex-direction: column; gap: 3px; }
    .add-detail label { font-size: 10.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: .4px; }
    .add-detail input, .add-detail select {
        border: 1px solid #d8e1dc;
        border-radius: 8px;
        padding: 7px 9px;
        font-size: 12px;
        min-height: 34px;
    }
</style>
@endpush

@section('content')
    @php
        $header = $data['header'] ?? [];
        $details = $data['details'] ?? [];
        $available = $data['available'] ?? [];
        $badge = ['ACTIVE' => 'st-active', 'QC' => 'st-qc', 'READY' => 'st-ready', 'ACC' => 'st-acc'];
        $num = function ($value) {
            $text = number_format((float) $value, 2, ',', '.');
            return rtrim(rtrim($text, '0'), ',');
        };
        $updated = !empty($data['generated_at'])
            ? \Carbon\Carbon::parse($data['generated_at'])->setTimezone('Asia/Jakarta')->format('H:i')
            : null;
        $status = $header['status'] ?? '';
        $fromQuery = (string) request()->query('from', '');
    @endphp

    <a href="{{ $backUrl }}" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
        Kembali ke daftar Receiving - Transaksi
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
            <span>{{ $errorMessage ?? 'Detail Receiving - Transaksi belum bisa dimuat.' }}</span>
            <a href="{{ $backUrl }}">Kembali ke daftar</a>
        </div>
    @else
        <section class="hero">
            <div class="hero-top">
                <div>
                    <h1>{{ $header['code'] }}</h1>
                    <p>Receiving transaksi / retur · {{ $header['warehouse'] }}</p>
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

        <div class="action-bar">
            <div>
                @if($status === 'ACTIVE')
                    <div class="note"><b>Draft transaksi.</b> Lengkapi daftar produk retur lalu publish ke QC.</div>
                @elseif($status === 'QC')
                    <div class="note"><b>Proses QC.</b> Catat hasil QC per produk, lalu publish to Ready.</div>
                @elseif($status === 'READY')
                    <div class="note"><b>Menunggu ACC di modul Trans.</b> Stok retur akan masuk setelah Admin melakukan ACC pada Receiving di Trans.</div>
                @else
                    <div class="note">Transaksi sudah final. Surat jalan bisa diunduh bila tersedia.</div>
                @endif
            </div>

            <div class="action-buttons">
                @if(in_array($status, ['ACTIVE', 'QC']) && !empty($data['can_publish']))
                    <form method="POST" action="{{ route('receiving_transaksi.publish', ['id' => $header['id']]) }}"
                          onsubmit="if (!confirm('Publish {{ $header['code'] }} ke tahap berikutnya?')) { return false; } this.querySelector('button').disabled = true; return true;">
                        @csrf
                        <input type="hidden" name="from" value="{{ $fromQuery }}">
                        <button type="submit" class="btn-acc">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                            {{ $status === 'ACTIVE' ? 'Publish ke QC' : 'Publish to Ready' }}
                        </button>
                    </form>
                @endif

                @if(!empty($data['can_pdf']))
                    <a href="{{ route('receiving_transaksi.pdf', ['id' => $header['id']]) }}" target="_blank" class="btn-line">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                        Surat Jalan
                    </a>
                @endif
            </div>
        </div>

        @if($status === 'ACTIVE' && !empty($data['can_update']))
            <section class="panel detail-panel">
                <header class="panel-head">
                    <div>
                        <h2>Tambah Produk Retur</h2>
                        <small>Produk diambil dari retur aktif yang masih memiliki sisa.</small>
                    </div>
                </header>
                @if(empty($available))
                    <div class="empty">
                        <span>
                            <b>Tidak ada produk retur tersisa</b>
                            <small>Semua qty retur sudah dimasukkan ke penerimaan ini.</small>
                        </span>
                    </div>
                @else
                    <div class="table-wrap" style="padding: 14px;">
                        <form method="POST" action="{{ route('receiving_transaksi.detail_add', ['id' => $header['id']]) }}" class="add-detail">
                            @csrf
                            <input type="hidden" name="from" value="{{ $fromQuery }}">
                            <div class="field" style="min-width: 320px;">
                                <label for="product_pack_id">Produk</label>
                                <select id="product_pack_id" name="product_pack_id" required>
                                    <option value="">— Pilih produk —</option>
                                    @foreach($available as $item)
                                        <option value="{{ $item['pack_id'] }}">
                                            {{ $item['code'] }} - {{ $item['name'] }} / {{ $item['pack'] }} (sisa {{ $num($item['qty_available']) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="field">
                                <label for="quantity">Qty</label>
                                <input type="number" id="quantity" name="quantity" min="0.01" step="0.01" required>
                            </div>
                            <div class="field">
                                <label for="no_batch">No Batch</label>
                                <input type="text" id="no_batch" name="no_batch" maxlength="50">
                            </div>
                            <div class="field" style="min-width: 180px;">
                                <label for="note">Catatan</label>
                                <input type="text" id="note" name="note" maxlength="255">
                            </div>
                            <button type="submit" class="btn-acc" style="padding: 8px 14px;">Tambah</button>
                        </form>
                    </div>
                @endif
            </section>
        @endif

        <section class="panel detail-panel">
            <header class="panel-head">
                <div>
                    <h2>Daftar Produk</h2>
                    <small>{{ count($details) }} baris · qty retur, hasil QC</small>
                </div>
                <span class="count-pill">{{ $header['code'] }}</span>
            </header>

            @if(empty($details))
                <div class="empty">
                    <span>
                        <b>Belum ada produk</b>
                        <small>Transaksi ini belum memiliki baris produk retur.</small>
                    </span>
                </div>
            @elseif($status === 'QC')
                <div class="table-wrap" style="padding: 14px;">
                    @foreach($details as $detail)
                        <div class="qc-card">
                            <div class="qc-card-head">
                                <div>
                                    <b>{{ $detail['product'] }}</b>
                                    <small>· {{ $detail['pack'] }} · qty retur {{ $num($detail['qty_po']) }}</small>
                                </div>
                                <small>
                                    QC {{ $num($detail['qc_total']) }} · sisa {{ $num($detail['remaining']) }}
                                </small>
                            </div>

                            @foreach($detail['qc_logs'] as $log)
                                <div class="qc-log">
                                    <span class="qty">{{ $num($log['qty']) }}</span>
                                    <span class="status {{ $log['status'] === 'OK' ? 'ok' : 'not' }}">{{ $log['status'] }}</span>
                                    @if(!empty($data['can_delete']))
                                        <form method="POST" action="{{ route('receiving_transaksi.qc_delete', ['id' => $header['id'], 'detailId' => $detail['id'], 'logId' => $log['id']]) }}"
                                              onsubmit="if (!confirm('Hapus log QC {{ $num($log['qty']) }}?')) { return false; } this.querySelector('button').disabled = true; return true;"
                                              style="margin-left:auto;">
                                            @csrf
                                            <input type="hidden" name="from" value="{{ $fromQuery }}">
                                            <input type="hidden" name="qty_qc" value="{{ $log['qty'] }}">
                                            <button type="submit" class="mini-btn ghost" title="Hapus log QC">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            @endforeach

                            @if($detail['remaining'] > 0 && !empty($data['can_update']))
                                <form method="POST" action="{{ route('receiving_transaksi.qc_store', ['id' => $header['id'], 'detailId' => $detail['id']]) }}" class="qc-form">
                                    @csrf
                                    <input type="hidden" name="from" value="{{ $fromQuery }}">
                                    <div class="field">
                                        <label>Qty QC</label>
                                        <input type="number" name="qty_qc" min="0.1" step="0.01" max="{{ $detail['remaining'] }}" required>
                                    </div>
                                    <div class="field">
                                        <label>Status</label>
                                        <select name="status_qc" required>
                                            <option value="OK">OK</option>
                                            <option value="NOT OK">NOT OK</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="mini-btn primary" style="min-height:34px;">Simpan QC</button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 44px;">No</th>
                                <th>Produk</th>
                                <th>Kemasan</th>
                                <th class="num">Qty Retur</th>
                                <th class="num">Qty Diterima</th>
                                <th class="num">Selisih</th>
                                <th class="num">QC</th>
                                <th>No Batch</th>
                                <th>Catatan</th>
                                @if($status === 'ACTIVE' && !empty($data['can_delete']))
                                    <th style="width: 70px;">Aksi</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($details as $detail)
                                <tr>
                                    <td class="cell-muted">{{ $detail['no'] }}</td>
                                    <td class="cell-code">{{ $detail['product'] }}</td>
                                    <td class="cell-muted">{{ $detail['pack'] }}</td>
                                    <td class="num">{{ $num($detail['qty_po']) }}</td>
                                    <td class="num">{{ $detail['qty_ri'] !== null ? $num($detail['qty_ri']) : '-' }}</td>
                                    <td class="num {{ $detail['selisih'] !== null && (float) $detail['selisih'] != 0 ? 'negative' : 'zero' }}">
                                        {{ $detail['selisih'] !== null ? $num($detail['selisih']) : '-' }}
                                    </td>
                                    <td class="num">{{ $num($detail['qc_total']) }}</td>
                                    <td class="cell-muted">{{ $detail['batch'] !== '' ? $detail['batch'] : '-' }}</td>
                                    <td class="cell-muted">{{ $detail['note'] !== '' ? $detail['note'] : '-' }}</td>
                                    @if($status === 'ACTIVE' && !empty($data['can_delete']))
                                        <td>
                                            <form method="POST" action="{{ route('receiving_transaksi.detail_delete', ['id' => $header['id'], 'detailId' => $detail['id']]) }}"
                                                  onsubmit="if (!confirm('Hapus produk {{ $detail['product'] }} dari penerimaan?')) { return false; } this.querySelector('button').disabled = true; return true;">
                                                @csrf
                                                <input type="hidden" name="from" value="{{ $fromQuery }}">
                                                <button type="submit" class="mini-btn ghost" title="Hapus detail">Hapus</button>
                                            </form>
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>
    @endif
@endsection
