@extends('layouts.app')

@section('title', 'Inventory - Stock')

@push('styles')
<style>
    .dhead {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        margin-bottom: 14px;
    }
    .dhead h1 { margin: 0 0 3px; font-size: 22px; letter-spacing: -.5px; color: #0b1f1a; }
    .dhead p { margin: 0; font-size: 12.5px; color: #64748b; }
    .dhead-side { display: flex; align-items: center; gap: 8px; padding-top: 3px; font-size: 11px; color: #94a3b8; }
    .icon-btn {
        display: grid; place-items: center;
        width: 32px; height: 32px;
        border-radius: 9px;
        background: #fff;
        border: 1px solid #e3ece8;
        color: #64748b;
        transition: background .15s ease, color .15s ease, border-color .15s ease;
    }
    .icon-btn:hover { background: #f0fdfa; color: #0f766e; border-color: #b8e6d8; }
    .icon-btn svg { width: 14px; height: 14px; }

    .filters {
        display: flex;
        align-items: flex-end;
        gap: 10px;
        flex-wrap: wrap;
        background: #fff;
        border: 1px solid #e6eeeb;
        border-radius: 14px;
        padding: 12px 14px;
        margin-bottom: 12px;
    }
    .field { display: flex; flex-direction: column; gap: 4px; }
    .field label { font-size: 10px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; color: #94a3b8; }
    .field select, .field input {
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
    .field select:focus, .field input:focus { border-color: #14b8a6; box-shadow: 0 0 0 3px rgba(20,184,166,.12); }
    .field.grow { flex: 1 1 220px; }
    .field.grow input { width: 100%; }
    .btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 8px 14px;
        border-radius: 8px;
        border: 1px solid transparent;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        background: linear-gradient(135deg, #14b8a6, #0f766e);
        color: #fff;
        min-height: 34px;
    }
    .btn:hover { background: linear-gradient(135deg, #0f766e, #115e59); }
    .btn svg { width: 13px; height: 13px; }
    .btn-reset {
        display: inline-flex;
        align-items: center;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        background: #f7faf9;
        border: 1px solid #e3ece8;
        min-height: 34px;
    }
    .btn-reset:hover { color: #0f766e; border-color: #b8e6d8; }

    .panel {
        background: #fff;
        border: 1px solid #e6eeeb;
        border-radius: 16px;
        box-shadow: 0 1px 2px rgba(15,23,42,.03), 0 16px 30px -30px rgba(15,23,42,.4);
        overflow: hidden;
    }
    .panel-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 13px 16px;
        border-bottom: 1px solid #f1f6f4;
    }
    .panel-head h2 { margin: 0; font-size: 13px; letter-spacing: -.1px; color: #0f172a; }
    .panel-head small { display: block; font-size: 10.5px; color: #94a3b8; margin-top: 1px; }
    .count-pill {
        padding: 3px 10px;
        border-radius: 999px;
        background: #eaf7f2;
        color: #0f766e;
        font-size: 10.5px;
        font-weight: 800;
        font-variant-numeric: tabular-nums;
    }

    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 12px; }
    thead th {
        text-align: left;
        padding: 9px 12px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: .45px;
        text-transform: uppercase;
        color: #94a3b8;
        background: #fafcfb;
        border-bottom: 1px solid #eef4f1;
        white-space: nowrap;
    }
    tbody td { padding: 10px 12px; border-bottom: 1px solid #f4f8f6; vertical-align: middle; }
    tbody tr:last-child td { border-bottom: 0; }
    tbody tr:hover { background: #f8fbfa; }
    .num { text-align: right; font-variant-numeric: tabular-nums; white-space: nowrap; }
    th.num { text-align: right; }
    .cell-code { font-weight: 700; color: #0f172a; font-variant-numeric: tabular-nums; }
    .cell-name { display: block; color: #64748b; font-size: 11px; margin-top: 1px; }
    .cell-muted { color: #64748b; }
    .negative { color: #dc2626; font-weight: 700; }
    .zero { color: #94a3b8; }
    .ks-link {
        color: #0f766e;
        font-weight: 700;
        border-bottom: 1px dashed #99d8ca;
    }
    .ks-link:hover { color: #115e59; border-bottom-style: solid; }

    .empty {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 22px 16px;
        color: #64748b;
        font-size: 12px;
    }
    .empty svg { width: 20px; height: 20px; color: #94a3b8; }
    .waiting {
        display: flex;
        align-items: center;
        gap: 13px;
        padding: 30px 18px;
        color: #64748b;
    }
    .waiting svg { width: 26px; height: 26px; color: #14b8a6; flex: none; }
    .waiting b { display: block; font-size: 12.5px; color: #0f172a; margin-bottom: 2px; }
    .waiting small { font-size: 11.5px; color: #94a3b8; }

    .pager {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        flex-wrap: wrap;
        padding: 11px 16px;
        border-top: 1px solid #f1f6f4;
        font-size: 11.5px;
        color: #64748b;
    }
    .pager .links { display: flex; gap: 6px; }
    .pager a, .pager span.disabled {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 7px;
        border: 1px solid #e3ece8;
        font-weight: 600;
        color: #0f766e;
        background: #fff;
    }
    .pager span.disabled { color: #cbd5e1; border-color: #eef4f1; }
    .pager a:hover { background: #f0fdfa; border-color: #b8e6d8; }

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
    }
    .error-note a {
        font-weight: 700;
        background: #92400e;
        color: #fff;
        padding: 6px 12px;
        border-radius: 7px;
        font-size: 11px;
    }

    .stock-modal {
        position: fixed;
        inset: 0;
        z-index: 80;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 18px;
        background: rgba(15, 23, 42, .45);
    }
    .stock-modal.open { display: flex; }
    .stock-modal-card {
        display: flex;
        flex-direction: column;
        width: min(1120px, 96vw);
        height: min(88vh, 900px);
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 24px 60px -20px rgba(15, 23, 42, .5);
        overflow: hidden;
    }
    .stock-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 11px 14px;
        border-bottom: 1px solid #eef4f1;
    }
    .stock-modal-head h3 { margin: 0; font-size: 13.5px; color: #0f172a; }
    .stock-modal-head small { display: block; font-size: 10.5px; color: #94a3b8; margin-top: 1px; }
    .stock-modal-close {
        display: grid;
        place-items: center;
        width: 30px;
        height: 30px;
        border-radius: 8px;
        border: 1px solid #e3ece8;
        background: #fff;
        color: #64748b;
        cursor: pointer;
        flex: none;
    }
    .stock-modal-close:hover { background: #f0fdfa; color: #0f766e; border-color: #b8e6d8; }
    .stock-modal-close svg { width: 14px; height: 14px; }
    .stock-modal-body { flex: 1; min-height: 0; background: #f8fbfa; }
    .stock-modal-body iframe { display: block; width: 100%; height: 100%; border: 0; background: #fff; }
    body.stock-modal-open { overflow: hidden; }

    @media (max-width: 620px) {
        .dhead h1 { font-size: 19px; }
        .field, .field.grow { flex: 1 1 100%; }
        .field select, .field input, .btn, .btn-reset { width: 100%; justify-content: center; }
        .stock-modal { padding: 0; }
        .stock-modal-card { width: 100vw; height: 100vh; border-radius: 0; }
    }
</style>
@endpush

@section('content')
    @php
        $updated = !empty($data['generated_at'])
            ? \Carbon\Carbon::parse($data['generated_at'])->setTimezone('Asia/Jakarta')->format('H:i')
            : null;
        $warehouses = $data['warehouses'] ?? [];
        $selectedWarehouse = (int) ($data['warehouse_id'] ?? ($filters['warehouse_id'] ?? 0));
        $rows = $data['rows'] ?? [];
        $brands = $data['brands'] ?? [];
        $packagingOptions = $data['packaging_options'] ?? [];
        $selectedBrand = $filters['brand'] ?? '';
        $selectedPackaging = $filters['packaging'] ?? '';
        $meta = $data['meta'] ?? ['page' => 1, 'last_page' => 1, 'total' => 0, 'per_page' => 25];
        $warehouseName = '';
        foreach ($warehouses as $warehouse) {
            if ((int) $warehouse['id'] === $selectedWarehouse) {
                $warehouseName = $warehouse['name'];
            }
        }
        $money = function ($value) {
            return number_format((float) $value, 2);
        };
    @endphp

    <div class="dhead">
        <div>
            <h1>Inventory - Stock</h1>
            <p>Stok per gudang{{ $warehouseName ? ' · ' . $warehouseName : '' }} — data langsung dari Trans.</p>
        </div>
        <div class="dhead-side">
            @if($applied && $updated)
                <span>Diperbarui {{ $updated }} WIB</span>
            @endif
            <a href="{{ route('stock', request()->query()) }}" class="icon-btn" title="Muat ulang" aria-label="Muat ulang">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 3v6h-6"/></svg>
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('stock') }}" class="filters">
        <div class="field">
            <label for="warehouse_id">Gudang</label>
            <select id="warehouse_id" name="warehouse_id" onchange="this.form.submit()">
                @foreach($warehouses as $warehouse)
                    <option value="{{ $warehouse['id'] }}" {{ (int) $warehouse['id'] === $selectedWarehouse ? 'selected' : '' }}>
                        {{ $warehouse['name'] }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label for="brand">Brand</label>
            <select id="brand" name="brand" onchange="this.form.submit()">
                <option value="">Semua brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand }}" {{ (string) $brand === $selectedBrand ? 'selected' : '' }}>{{ $brand }}</option>
                @endforeach
            </select>
        </div>
        <div class="field">
            <label for="packaging">Kemasan</label>
            <select id="packaging" name="packaging" onchange="this.form.submit()">
                <option value="">Semua kemasan</option>
                @foreach($packagingOptions as $packaging)
                    <option value="{{ $packaging['id'] }}" {{ (string) $packaging['id'] === $selectedPackaging ? 'selected' : '' }}>{{ $packaging['pack_name'] }}</option>
                @endforeach
            </select>
        </div>
        <div class="field grow">
            <label for="search">Cari</label>
            <input type="text" id="search" name="search" value="{{ $filters['search'] }}" placeholder="Kode, nama produk, atau brand">
        </div>
        <button type="submit" class="btn">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
            Cari
        </button>
        @if($applied)
            <a href="{{ route('stock') }}" class="btn-reset">Reset</a>
        @endif
    </form>

    @if(!$data)
        <div class="error-note">
            <span>{{ $errorMessage ?? 'Data stock belum bisa dimuat.' }}</span>
            <a href="{{ route('stock') }}">Coba lagi</a>
        </div>
    @else
        <section class="panel">
            <header class="panel-head">
                <div>
                    <h2>Daftar Stock</h2>
                    <small>Stock = saldo tercatat · KS = pergerakan terakhir</small>
                </div>
                @if($applied)
                    <span class="count-pill">{{ $meta['total'] }} item</span>
                @endif
            </header>

            @if(!$applied)
                <div class="waiting">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 5h16"/><path d="M7 12h10"/><path d="M10 19h4"/></svg>
                    <span>
                        <b>Pilih filter untuk menampilkan stok</b>
                        <small>Pilih brand atau kemasan, atau masukkan kata kunci, lalu tekan Cari.</small>
                    </span>
                </div>
            @elseif(empty($rows))
                <div class="empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/></svg>
                    Tidak ada produk yang cocok dengan filter ini.
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 44px;">No</th>
                                <th>Produk</th>
                                <th>Brand</th>
                                <th>Kemasan</th>
                                <th>Tipe</th>
                                <th class="num">Stock</th>
                                <th class="num">KS</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                                <tr>
                                    <td class="cell-muted">{{ $row['no'] }}</td>
                                    <td>
                                        <span class="cell-code">{{ $row['code'] }}</span>
                                        <span class="cell-name">{{ $row['product'] }}</span>
                                    </td>
                                    <td class="cell-muted">{{ $row['brand'] }}</td>
                                    <td class="cell-muted">{{ $row['packaging'] }}</td>
                                    <td class="cell-muted">{{ $row['type'] }}</td>
                                    <td class="num {{ $row['stock'] < 0 ? 'negative' : ($row['stock'] == 0 ? 'zero' : '') }}">{{ $money($row['stock']) }}</td>
                                    <td class="num {{ $row['ks'] < 0 ? 'negative' : ($row['ks'] == 0 ? 'zero' : '') }}">
                                        <a href="{{ route('stock.detail', ['warehouseId' => $selectedWarehouse, 'encoded' => $row['encoded_id'], 'from' => request()->getQueryString()]) }}"
                                           class="ks-link" title="Lihat kartu stock" data-stock-modal
                                           data-modal-url="{{ route('stock.detail', ['warehouseId' => $selectedWarehouse, 'encoded' => $row['encoded_id'], 'from' => request()->getQueryString(), 'embed' => 1]) }}"
                                           data-modal-title="{{ $row['code'] }} · {{ $row['packaging'] }}">{{ $money($row['ks']) }}</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if($meta['last_page'] > 1)
                    <div class="pager">
                        <span>Halaman {{ $meta['page'] }} dari {{ $meta['last_page'] }} · {{ $meta['total'] }} item</span>
                        <div class="links">
                            @if($meta['page'] > 1)
                                <a href="{{ route('stock', array_merge(request()->query(), ['page' => $meta['page'] - 1])) }}">
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
                                    Sebelumnya
                                </a>
                            @else
                                <span class="disabled">Sebelumnya</span>
                            @endif
                            @if($meta['page'] < $meta['last_page'])
                                <a href="{{ route('stock', array_merge(request()->query(), ['page' => $meta['page'] + 1])) }}">
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

    <div class="stock-modal" id="stock-modal" aria-hidden="true">
        <div class="stock-modal-card" role="dialog" aria-modal="true" aria-labelledby="stock-modal-title">
            <div class="stock-modal-head">
                <div>
                    <h3 id="stock-modal-title">Kartu Stock</h3>
                    <small id="stock-modal-sub">Pergerakan stock per periode</small>
                </div>
                <button type="button" class="stock-modal-close" id="stock-modal-close" aria-label="Tutup">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M6 6l12 12"/><path d="M18 6L6 18"/></svg>
                </button>
            </div>
            <div class="stock-modal-body">
                <iframe id="stock-modal-frame" title="Kartu Stock"></iframe>
            </div>
        </div>
    </div>

    <script>
        (function () {
            Wms.bindOnce('stock-index', function () {
                function els() {
                    return {
                        modal: document.getElementById('stock-modal'),
                        frame: document.getElementById('stock-modal-frame'),
                        title: document.getElementById('stock-modal-title'),
                        closeBtn: document.getElementById('stock-modal-close')
                    };
                }

                function open(url, label) {
                    var el = els();
                    if (!el.modal || !el.frame) { return; }
                    el.title.textContent = label || 'Kartu Stock';
                    el.frame.src = url;
                    el.modal.classList.add('open');
                    el.modal.setAttribute('aria-hidden', 'false');
                    document.body.classList.add('stock-modal-open');
                    el.closeBtn.focus();
                }

                function close() {
                    var el = els();
                    if (!el.modal || !el.frame) { return; }
                    el.modal.classList.remove('open');
                    el.modal.setAttribute('aria-hidden', 'true');
                    document.body.classList.remove('stock-modal-open');
                    el.frame.src = 'about:blank';
                }

                // Capture phase: batalkan navigasi live sebelum handler link global.
                document.addEventListener('click', function (event) {
                    if (!event.target.closest) { return; }
                    var link = event.target.closest('[data-stock-modal]');
                    if (link) {
                        event.preventDefault();
                        open(link.getAttribute('data-modal-url'), link.getAttribute('data-modal-title'));
                        return;
                    }
                    if (event.target.closest('#stock-modal-close') || event.target.id === 'stock-modal') {
                        close();
                    }
                }, true);

                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') { close(); }
                });
            });
        })();
    </script>
@endsection
