@extends('layouts.app')

@section('title', 'Detail Mutasi')

@push('styles')
@include('partials.list-styles')
@include('partials.detail-styles')
<style>
    .stepper {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 8px;
        margin-bottom: 12px;
    }
    .step-item {
        display: flex;
        align-items: center;
        gap: 9px;
        background: #fff;
        border: 1px solid #e6eeeb;
        border-radius: 12px;
        padding: 10px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #94a3b8;
    }
    .step-item .n {
        display: grid;
        place-items: center;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: #eef4f1;
        color: #94a3b8;
        font-size: 11px;
        font-weight: 800;
        flex: none;
    }
    .step-item.active { border-color: #b8e6d8; color: #0f766e; }
    .step-item.active .n { background: linear-gradient(135deg, #14b8a6, #0f766e); color: #fff; }
    .step-item.done { color: #15803d; }
    .step-item.done .n { background: #dcfce7; color: #15803d; }

    .check-list { display: flex; flex-direction: column; gap: 8px; }
    .check-row {
        display: flex;
        align-items: center;
        gap: 10px;
        border: 1px solid #eef4f1;
        border-radius: 10px;
        padding: 10px 12px;
        background: #fcfdfd;
    }
    .check-row input[type="checkbox"] { width: 16px; height: 16px; accent-color: #0f766e; }
    .check-row .info { flex: 1; min-width: 0; }
    .check-row .info b { display: block; font-size: 12.5px; color: #0f172a; }
    .check-row .info small { color: #64748b; font-size: 11px; }
    .check-row .qty { font-weight: 700; font-variant-numeric: tabular-nums; white-space: nowrap; }

    .field select, .field input[type="file"] {
        font: inherit;
        font-size: 12px;
        padding: 8px 10px;
        border-radius: 8px;
        border: 1px solid #dbe6e1;
        background: #fff;
        color: #1e293b;
        outline: none;
        min-height: 34px;
    }

    .bukti-img {
        display: block;
        max-width: 320px;
        width: 100%;
        border-radius: 10px;
        border: 1px solid #e6eeeb;
        margin-top: 8px;
    }

    @media (max-width: 620px) {
        .stepper { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')
    @php
        $header = $data['header'] ?? [];
        $details = $data['details'] ?? [];
        $flags = $data['flags'] ?? [];
        $step = (int) ($data['step'] ?? 1);
        $statusBarang = (int) ($header['status_barang'] ?? 0);
        $from = request()->query('from');
        $params = function (array $extra = []) use ($type, $mutasiId, $from) {
            $query = array_merge(['type' => $type, 'id' => $mutasiId], $extra);
            if ($from) {
                $query['from'] = $from;
            }
            return $query;
        };
        $num = function ($value) {
            return number_format((float) $value, 2);
        };
        $barangLabel = [0 => 'Pending', 1 => 'Belum Diambil', 2 => 'Diambil'];
        $barangBadge = [0 => 'st-neutral', 1 => 'st-active', 2 => 'st-acc'];
    @endphp

    <a href="{{ $backUrl }}" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
        Kembali ke daftar Checker Mutasi
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
            <span>{{ $errorMessage ?? 'Detail mutasi belum bisa dimuat.' }}</span>
            <a href="{{ $backUrl }}">Kembali ke daftar</a>
        </div>
    @else
        <section class="hero">
            <div class="hero-top">
                <div>
                    <h1>{{ $header['code'] ?? '-' }}</h1>
                    <p>{{ $header['type_label'] ?? '-' }} · {{ $header['date'] ?? '-' }}</p>
                </div>
                <div class="hero-chips">
                    <span class="badge-status {{ $barangBadge[$statusBarang] ?? 'st-neutral' }}">{{ $barangLabel[$statusBarang] ?? '-' }}</span>
                    <span class="chip">Print {{ (int) ($header['print_count'] ?? 0) }}x</span>
                </div>
            </div>

            <div class="meta-grid">
                <div class="meta-item">
                    <span>Gudang Asal</span>
                    <b>{{ $header['warehouse'] ?? '-' }}</b>
                </div>
                @if(($header['warehouse_to'] ?? null) !== null)
                    <div class="meta-item">
                        <span>Gudang Tujuan</span>
                        <b>{{ $header['warehouse_to'] }}</b>
                    </div>
                @endif
                @if(($header['customer'] ?? '-') !== '-')
                    <div class="meta-item">
                        <span>Customer</span>
                        <b>{{ $header['customer'] }}</b>
                    </div>
                @endif
                @if(($header['so'] ?? '-') !== '-')
                    <div class="meta-item">
                        <span>Sales Order</span>
                        <b>{{ $header['so'] }}</b>
                    </div>
                @endif
                <div class="meta-item">
                    <span>Jumlah Item</span>
                    <b>{{ count($details) }}</b>
                </div>
            </div>
        </section>

        <div class="stepper">
            <div class="step-item {{ $step === 1 ? 'active' : ($step > 1 ? 'done' : '') }}">
                <span class="n">1</span> Checklist
            </div>
            <div class="step-item {{ $step === 2 ? 'active' : ($step > 2 ? 'done' : '') }}">
                <span class="n">2</span> Print SJ
            </div>
            <div class="step-item {{ $step >= 3 ? 'active' : '' }}">
                <span class="n">3</span> Update Barang
            </div>
        </div>

        @if($step === 1)
            <section class="panel detail-panel">
                <header class="panel-head">
                    <div>
                        <h2>Step 1 — Checklist Produk</h2>
                        <small>Semua produk wajib dicentang. Stok fisik gudang asal dipotong setelah disimpan.</small>
                    </div>
                    <span class="count-pill">{{ count($details) }} item</span>
                </header>

                <form method="POST" action="{{ route('mutasi.checklist', $params()) }}" id="form-checklist" style="padding: 14px 16px;">
                    @csrf
                    <div class="check-list">
                        @foreach($details as $detail)
                            <label class="check-row">
                                <input type="checkbox" name="items[]" value="{{ $detail['id'] }}">
                                <span class="info">
                                    <b>{{ $detail['product'] }}</b>
                                    <small>{{ $detail['packaging'] }}</small>
                                </span>
                                <span class="qty">{{ $num($detail['qty']) }}</span>
                            </label>
                        @endforeach
                    </div>

                    <div class="action-buttons" style="justify-content: flex-end; margin-top: 14px;">
                        <button type="button" class="btn-line" id="check-all">Centang semua</button>
                        <button type="submit" class="btn">Save &amp; Lanjut</button>
                    </div>
                </form>
            </section>
        @elseif($step === 2)
            <section class="panel detail-panel">
                <header class="panel-head">
                    <div>
                        <h2>Step 2 — Cetak Surat Jalan</h2>
                        <small>Cetak SJ sebelum lanjut. Dokumen dibatasi maksimal 2 kali cetak.</small>
                    </div>
                </header>

                <div style="padding: 14px 16px;">
                    <div class="action-buttons">
                        @if(!empty($flags['print_blocked']))
                            <span class="notice err" style="margin: 0;">Batas cetak sudah tercapai. Hubungi admin untuk cetak ulang.</span>
                        @else
                            <a href="{{ route('mutasi.pdf', $params()) }}" target="_blank" rel="noopener" class="btn-line">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><path d="M6 18H4a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                                Print SJ
                            </a>
                        @endif
                    </div>

                    <div class="action-buttons" style="justify-content: flex-end; margin-top: 16px;">
                        <form method="POST" action="{{ route('mutasi.cancel', $params()) }}"
                              data-confirm="Batalkan checklist? Stok yang sudah dipotong akan dikembalikan.">
                            @csrf
                            <button type="submit" class="btn-line">Cancel</button>
                        </form>
                        <form method="POST" action="{{ route('mutasi.next', $params()) }}">
                            @csrf
                            <button type="submit" class="btn">Next Step</button>
                        </form>
                    </div>
                </div>
            </section>
        @else
            <section class="panel detail-panel">
                <header class="panel-head">
                    <div>
                        <h2>Step 3 — Update Status Barang</h2>
                        <small>{{ $statusBarang === 2 ? 'Barang sudah diambil dan stok tercatat.' : 'Tandai barang belum diambil atau sudah diambil beserta bukti foto.' }}</small>
                    </div>
                </header>

                <div style="padding: 14px 16px;">
                    @if(!empty($flags['can_update']))
                        <form method="POST" action="{{ route('mutasi.barang', $params()) }}" enctype="multipart/form-data" id="form-barang">
                            @csrf
                            <div class="field" style="max-width: 280px;">
                                <label for="status_barang">Status Barang</label>
                                <select id="status_barang" name="status_barang" required>
                                    <option value="">-- Pilih Status --</option>
                                    @if($statusBarang === 0)
                                        <option value="1">BELUM DIAMBIL</option>
                                    @endif
                                    <option value="2">DIAMBIL</option>
                                </select>
                            </div>

                            <div class="field" id="upload-wrapper" style="max-width: 360px; margin-top: 12px; display: none;">
                                <label for="image">Bukti Barang Diambil (wajib)</label>
                                <input type="file" id="image" name="image" accept="image/*">
                                <small style="color: #64748b; font-size: 11px;">JPG / PNG, maksimal 2 MB</small>
                            </div>

                            <div class="action-buttons" style="justify-content: flex-end; margin-top: 14px;">
                                <button type="submit" class="btn">Update Status</button>
                            </div>
                        </form>
                    @endif

                    <div class="table-wrap" style="margin-top: 14px;">
                        <table>
                            <thead>
                                <tr>
                                    <th style="width: 44px;">No</th>
                                    <th>Produk</th>
                                    <th>Kemasan</th>
                                    <th class="num" style="width: 110px;">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($details as $detail)
                                    <tr>
                                        <td class="cell-muted">{{ $detail['no'] }}</td>
                                        <td class="cell-code">{{ $detail['product'] }}</td>
                                        <td class="cell-muted">{{ $detail['packaging'] }}</td>
                                        <td class="num">{{ $num($detail['qty']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(!empty($flags['can_view_image']))
                        <a href="{{ route('mutasi.image', $params()) }}" target="_blank" rel="noopener" class="btn-line" style="margin-top: 14px;">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                            Lihat Bukti Foto
                        </a>
                    @endif
                </div>
            </section>
        @endif

        @if($step === 1 && empty($details))
            <div class="notice err">Mutasi ini tidak memiliki detail produk.</div>
        @endif
    @endif

<script>
    (function () {
        Wms.bindOnce('mutasi-show', function () {
            document.addEventListener('click', function (event) {
                if (!event.target.closest || !event.target.closest('#check-all')) { return; }
                var form = document.getElementById('form-checklist');
                if (form) {
                    form.querySelectorAll('input[name="items[]"]').forEach(function (input) {
                        input.checked = true;
                    });
                }
            });
        });

        Wms.onSwap(function (main) {
            var formChecklist = main.querySelector('#form-checklist');
            if (formChecklist) {
                // Dipasang di form (bukan document) agar validasi berjalan sebelum submit live.
                formChecklist.addEventListener('submit', function (event) {
                    var inputs = formChecklist.querySelectorAll('input[name="items[]"]');
                    var checked = formChecklist.querySelectorAll('input[name="items[]"]:checked');

                    if (inputs.length !== checked.length) {
                        event.preventDefault();
                        Wms.alert('Semua produk wajib dicentang.', 'Checklist belum lengkap');
                    }
                });
            }

            var status = main.querySelector('#status_barang');
            var upload = main.querySelector('#upload-wrapper');

            function toggleUpload() {
                if (!status || !upload) { return; }
                upload.style.display = status.value === '2' ? '' : 'none';
            }

            if (status) {
                status.addEventListener('change', toggleUpload);
                toggleUpload();
            }
        });
    })();
</script>
@endsection
