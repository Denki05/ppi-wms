@extends('layouts.app')

@section('title', 'Detail Receiving')

@push('styles')
@include('partials.list-styles')
@include('partials.detail-styles')
<style>
    .qc-sum {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
        font-size: 11.5px;
        color: #64748b;
        margin-bottom: 10px;
    }
    .qc-sum b { color: #0f172a; font-variant-numeric: tabular-nums; }
    .qc-modal {
        position: fixed;
        inset: 0;
        z-index: 120;
        display: none;
    }
    .qc-modal.open { display: block; }
    .qc-modal-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, .45);
    }
    .qc-modal-dialog {
        position: relative;
        margin: 6vh auto;
        width: calc(100% - 32px);
        max-width: 620px;
        max-height: 86vh;
        overflow: auto;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, .28);
    }
    .qc-modal-head {
        position: sticky;
        top: 0;
        z-index: 1;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        padding: 14px 16px;
        background: #fff;
        border-bottom: 1px solid #eef2f7;
    }
    .qc-modal-head b { color: #0f172a; }
    .qc-modal-head small { display: block; color: #64748b; margin-top: 2px; }
    .qc-modal-close {
        flex: none;
        width: 30px;
        height: 30px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #fff;
        color: #64748b;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
    }
    .qc-modal-close:hover { background: #f8fafc; color: #0f172a; }
    .qc-modal-body { padding: 14px 16px 16px; }
    .btn-acc:disabled, .mini-btn:disabled {
        opacity: .5;
        cursor: not-allowed;
    }
    .qc-log {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
        padding: 6px 0;
        border-top: 1px dashed #e2e8f0;
        font-size: 12px;
    }
    .qc-log .qty { font-weight: 700; font-variant-numeric: tabular-nums; min-width: 56px; }
    .qc-log .status { font-weight: 700; }
    .qc-log .status.ok { color: #15803d; }
    .qc-log .status.not { color: #b91c1c; }
    .qc-log .status.hold { color: #b45309; }
    .qc-tag {
        display: inline-block;
        padding: 1px 7px;
        border-radius: 999px;
        font-size: 9.5px;
        font-weight: 700;
        letter-spacing: .3px;
        text-transform: uppercase;
        background: #eef2f7;
        color: #475569;
    }
    .qc-tag.ok { background: #e8f7f1; color: #0f766e; }
    .qc-tag.warn { background: #fff7e6; color: #b45309; }
    .qc-log-actions { margin-left: auto; display: flex; gap: 6px; }
    .qc-empty { padding: 6px 0; font-size: 12px; color: #94a3b8; border-top: 1px dashed #e2e8f0; }
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
    .qc-check {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 34px;
        font-size: 12px;
        font-weight: 600;
        color: #475569;
    }
</style>
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
        $qcIncomplete = false;
        $qcPendingApproval = false;
        if ($status === 'QC') {
            foreach ($rows as $row) {
                if ((float) $row['qc_total'] <= 0) {
                    $qcIncomplete = true;
                }
                foreach ($row['qc_logs'] as $log) {
                    if ($log['is_sellable'] && !$log['is_approved']) {
                        $qcPendingApproval = true;
                    }
                }
            }
        }
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
                    <div class="note"><b>Proses QC.</b> Catat hasil QC per produk; tandai <b>Saleable</b> bila layak jual, lalu Approve sebelum Publish to Ready.</div>
                    @if($qcIncomplete)
                        <div class="note warn">Masih ada produk tanpa log QC. Setiap produk wajib punya qty QC &gt; 0 sebelum Publish to Ready.</div>
                    @elseif($qcPendingApproval)
                        <div class="note warn">Masih ada log QC Saleable yang belum di-approve. Approve dulu agar Publish to Ready tidak ditolak.</div>
                    @endif
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
                    @php($qcBlocked = $qcIncomplete || $qcPendingApproval)
                    <form method="POST" action="{{ route('receiving.publish', ['id' => $header['id']]) }}"
                          onsubmit="if (!confirm('Publish penerimaan {{ $header['code'] }} ke tahap Ready?')) { return false; } this.querySelector('button').disabled = true; return true;">
                        @csrf
                        <input type="hidden" name="from" value="{{ $fromQuery }}">
                        <button type="submit" class="btn-acc" {{ $qcBlocked ? 'disabled' : '' }}
                                title="{{ $qcBlocked ? ($qcIncomplete ? 'Semua produk harus punya log QC lebih dulu.' : 'Approve semua log QC Saleable lebih dulu.') : '' }}">
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
            @elseif($status === 'QC')
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 44px;">No</th>
                                <th>Produk</th>
                                <th>Kemasan</th>
                                <th class="num">Qty PO</th>
                                <th class="num">QC OK</th>
                                <th class="num">QC NOT OK</th>
                                <th class="num">QC Disetujui</th>
                                <th class="num">Sisa</th>
                                <th>No Batch</th>
                                <th class="num" style="width: 110px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td class="cell-muted">{{ $row['no'] }}</td>
                                    <td class="cell-code">{{ $row['product'] }}</td>
                                    <td class="cell-muted">{{ $row['pack'] }}</td>
                                    <td class="num">{{ $num($row['qty_po']) }}</td>
                                    <td class="num {{ (float) $row['qc_ok'] > 0 ? 'qc-ok' : 'zero' }}">{{ $num($row['qc_ok']) }}</td>
                                    <td class="num {{ (float) $row['qc_not_ok'] > 0 ? 'qc-bad' : 'zero' }}">{{ $num($row['qc_not_ok']) }}</td>
                                    <td class="num {{ (float) $row['qc_approved'] > 0 ? 'qc-ok' : 'zero' }}">{{ $num($row['qc_approved']) }}</td>
                                    <td class="num {{ (float) $row['remaining'] > 0 ? '' : 'zero' }}">{{ $num($row['remaining']) }}</td>
                                    <td class="cell-muted">{{ $row['batch'] !== '' ? $row['batch'] : '-' }}</td>
                                    <td class="num">
                                        <button type="button" class="mini-btn primary" data-qc-open="qc-modal-{{ $row['id'] }}">Proses QC</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @foreach($rows as $row)
                    <div class="qc-modal" id="qc-modal-{{ $row['id'] }}" aria-hidden="true">
                        <div class="qc-modal-backdrop" data-qc-close></div>
                        <div class="qc-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="qc-modal-title-{{ $row['id'] }}">
                            <div class="qc-modal-head">
                                <div>
                                    <b id="qc-modal-title-{{ $row['id'] }}">{{ $row['product'] }}</b>
                                    <small>
                                        {{ $row['pack'] }}@if($row['batch'] !== '') · batch {{ $row['batch'] }}@endif
                                        @if($row['note'] !== '') · {{ $row['note'] }}@endif
                                    </small>
                                </div>
                                <button type="button" class="qc-modal-close" data-qc-close aria-label="Tutup">&times;</button>
                            </div>
                            <div class="qc-modal-body">
                                <div class="qc-sum">
                                    <span>Qty PO <b>{{ $num($row['qty_po']) }}</b></span>
                                    <span>QC <b>{{ $num($row['qc_total']) }}</b></span>
                                    <span>Sisa <b>{{ $num($row['remaining']) }}</b></span>
                                </div>

                                @forelse($row['qc_logs'] as $log)
                                    <div class="qc-log">
                                        <span class="qty">{{ $num($log['qty']) }}</span>
                                        <span class="status {{ $log['status'] === 'OK' ? 'ok' : ($log['status'] === 'HOLD' ? 'hold' : 'not') }}">{{ $log['status'] }}</span>
                                        @if($log['is_sellable'])
                                            <span class="qc-tag warn">Saleable</span>
                                        @endif
                                        @if($log['is_approved'])
                                            <span class="qc-tag ok">Masuk stok</span>
                                        @endif

                                        <span class="qc-log-actions">
                                            @if($log['status'] === 'OK' && $log['is_sellable'] && !$log['is_approved'] && !empty($data['can_approve_qc']))
                                                <form method="POST" action="{{ route('receiving.qc_approve', ['id' => $header['id'], 'detailId' => $row['id'], 'logId' => $log['id']]) }}"
                                                      onsubmit="if (!confirm('Approve QC {{ $num($log['qty']) }}? Stok akan masuk dan PO dipotong.')) { return false; } this.querySelector('button').disabled = true; return true;">
                                                    @csrf
                                                    <input type="hidden" name="from" value="{{ $fromQuery }}">
                                                    <input type="hidden" name="qty_qc" value="{{ $log['qty'] }}">
                                                    <input type="hidden" name="status_qc" value="{{ $log['status_code'] }}">
                                                    <input type="hidden" name="is_sellable" value="{{ $log['is_sellable'] }}">
                                                    <input type="hidden" name="is_approved" value="{{ $log['is_approved'] }}">
                                                    <button type="submit" class="mini-btn primary" title="Approve QC saleable">Approve</button>
                                                </form>
                                            @endif
                                            @if(!($log['is_sellable'] && $log['is_approved']) && !empty($data['can_delete_qc']))
                                                <form method="POST" action="{{ route('receiving.qc_delete', ['id' => $header['id'], 'detailId' => $row['id'], 'logId' => $log['id']]) }}"
                                                      onsubmit="if (!confirm('Hapus log QC {{ $num($log['qty']) }}?')) { return false; } this.querySelector('button').disabled = true; return true;">
                                                    @csrf
                                                    <input type="hidden" name="from" value="{{ $fromQuery }}">
                                                    <input type="hidden" name="qty_qc" value="{{ $log['qty'] }}">
                                                    <input type="hidden" name="status_qc" value="{{ $log['status_code'] }}">
                                                    <input type="hidden" name="is_sellable" value="{{ $log['is_sellable'] }}">
                                                    <input type="hidden" name="is_approved" value="{{ $log['is_approved'] }}">
                                                    <button type="submit" class="mini-btn ghost" title="Hapus log QC">Hapus</button>
                                                </form>
                                            @endif
                                        </span>
                                    </div>
                                @empty
                                    <div class="qc-empty" style="border-top: 0;">Belum ada log QC untuk produk ini.</div>
                                @endforelse

                                @if($row['remaining'] > 0 && !empty($data['can_qc']))
                                    <form method="POST" action="{{ route('receiving.qc_store', ['id' => $header['id'], 'detailId' => $row['id']]) }}" class="qc-form">
                                        @csrf
                                        <input type="hidden" name="from" value="{{ $fromQuery }}">
                                        <div class="field">
                                            <label>Qty QC</label>
                                            <input type="number" name="qty_qc" min="0.1" step="0.01" max="{{ $row['remaining'] }}" required>
                                        </div>
                                        <div class="field">
                                            <label>Status</label>
                                            <select name="status_qc" required>
                                                <option value="OK">OK</option>
                                                <option value="NOT OK">NOT OK</option>
                                            </select>
                                        </div>
                                        <label class="qc-check" title="Tandai bila barang layak jual; wajib di-Approve sebelum Publish to Ready">
                                            <input type="checkbox" name="is_sellable" value="1">
                                            Saleable
                                        </label>
                                        <button type="submit" class="mini-btn primary" style="min-height:34px;">Simpan QC</button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <script>
                    (function () {
                        document.querySelectorAll('[data-qc-open]').forEach(function (btn) {
                            btn.addEventListener('click', function () {
                                var modal = document.getElementById(btn.getAttribute('data-qc-open'));
                                if (modal) {
                                    modal.classList.add('open');
                                    modal.setAttribute('aria-hidden', 'false');
                                    document.body.style.overflow = 'hidden';
                                }
                            });
                        });
                        document.querySelectorAll('.qc-modal').forEach(function (modal) {
                            modal.addEventListener('click', function (event) {
                                if (event.target.closest('[data-qc-close]')) {
                                    modal.classList.remove('open');
                                    modal.setAttribute('aria-hidden', 'true');
                                    document.body.style.overflow = '';
                                }
                            });
                        });
                        document.addEventListener('keydown', function (event) {
                            if (event.key !== 'Escape') { return; }
                            document.querySelectorAll('.qc-modal.open').forEach(function (modal) {
                                modal.classList.remove('open');
                                modal.setAttribute('aria-hidden', 'true');
                            });
                            document.body.style.overflow = '';
                        });
                    })();
                </script>
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
