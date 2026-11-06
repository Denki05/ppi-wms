@extends('layouts.app')

@section('title', 'Detail DO')

@push('styles')
@include('partials.list-styles')
@include('partials.detail-styles')
@endpush

@section('content')
    @php
        $header = $data['header'] ?? [];
        $items = $data['items'] ?? [];
        $cost = $data['cost'] ?? [];
        $actions = $data['actions'] ?? [];
        $status = $header['status'] ?? '';
        $badge = ['READY' => 'st-ready', 'PACKED' => 'st-qc', 'DELIVERING' => 'st-active', 'DELIVERED' => 'st-acc'];
        $ekspedisi = session('wms.bootstrap.ekspedisi', []);
        $num = function ($value) {
            $text = number_format((float) $value, 2, ',', '.');
            return rtrim(rtrim($text, '0'), ',');
        };
        $idr = function ($value) {
            return number_format((float) $value, 0, ',', '.');
        };
        $updated = !empty($data['generated_at'])
            ? \Carbon\Carbon::parse($data['generated_at'])->setTimezone('Asia/Jakarta')->format('H:i')
            : null;
    @endphp

    <a href="{{ $backUrl }}" class="back-link">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M15 6l-6 6 6 6"/></svg>
        Kembali ke Checker Transaksi (DO)
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
            <span>{{ $errorMessage ?? 'Detail DO belum bisa dimuat.' }}</span>
            <a href="{{ $backUrl }}">Kembali ke daftar</a>
        </div>
    @else
        <section class="hero">
            <div class="hero-top">
                <div>
                    <h1>{{ $header['do_code'] }}</h1>
                    <p>{{ $header['customer'] }} &middot; {{ $header['warehouse'] }}</p>
                </div>
                <div class="hero-chips">
                    <span class="badge-status {{ $badge[$status] ?? 'st-neutral' }}">{{ $status }}</span>
                    @if($header['is_kurs_hold'])
                        <span class="tag" style="background:#fffbeb;color:#b45309;margin-left:0;">Kurs belum di-set</span>
                    @endif
                    @if($header['void_status'])
                        <span class="tag" style="margin-left:0;">Pengajuan void</span>
                    @endif
                    @if($header['print_count'] > 0)
                        <span class="chip">SPK dicetak {{ $header['print_count'] }}x</span>
                    @endif
                    @if($updated)
                        <span class="chip">Diperbarui {{ $updated }} WIB</span>
                    @endif
                </div>
            </div>

            <div class="meta-grid">
                <div class="meta-item">
                    <span>Referensi SO</span>
                    <b>{{ $header['so_code'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Gudang</span>
                    <b>{{ $header['warehouse'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Customer</span>
                    <b>{{ $header['customer'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Alamat</span>
                    <b>{{ $header['customer_address'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Kontak</span>
                    <b>{{ $header['contact_label'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Alamat Kirim</span>
                    <b>{{ $header['contact_address'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Telepon</span>
                    <b>{{ $header['contact_phone'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Ekspedisi</span>
                    <b>{{ $header['ekspedisi'] }}</b>
                </div>
                <div class="meta-item">
                    <span>Tipe Transaksi</span>
                    <b>{{ $header['type_transaction'] ?: '-' }}</b>
                </div>
                <div class="meta-item">
                    <span>Dibuat</span>
                    <b>{{ $header['created_at'] }}</b>
                </div>
                @if($status === 'DELIVERED')
                    <div class="meta-item">
                        <span>Tanggal Dikirim</span>
                        <b>{{ $header['date_sent'] }}</b>
                    </div>
                @endif
            </div>
        </section>

        <section class="panel detail-panel">
            <header class="panel-head">
                <div>
                    <h2>Daftar Barang</h2>
                    <small>{{ count($items) }} baris produk</small>
                </div>
                <span class="count-pill">{{ $header['do_code'] }}</span>
            </header>

            @if(empty($items))
                <div class="empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 8l-9-5-9 5 9 5 9-5z"/><path d="M3 8v8l9 5 9-5V8"/><path d="M12 13v8"/></svg>
                    <span>
                        <b>Belum ada barang</b>
                        <small>DO ini belum memiliki baris produk.</small>
                    </span>
                </div>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 44px;">No</th>
                                <th>Produk</th>
                                <th>Kode</th>
                                <th>Kemasan</th>
                                <th class="num">Qty</th>
                                <th class="num">Total Packing</th>
                                <th>Catatan</th>
                                @if(!empty($actions['can_pack']))
                                    <th style="width: 70px;" class="num">
                                        Cek
                                        <input type="checkbox" id="check-all-items" style="vertical-align: middle; margin-left: 2px;">
                                    </th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $row)
                                <tr>
                                    <td class="cell-muted">{{ $row['no'] }}</td>
                                    <td class="cell-code">{{ $row['product_name'] }}</td>
                                    <td class="cell-muted">{{ $row['product_code'] }}</td>
                                    <td class="cell-muted">{{ $row['packaging'] }}</td>
                                    <td class="num">{{ $num($row['qty']) }}</td>
                                    <td class="num">{{ $row['total_packing'] == 0 ? 'Free' : $num($row['total_packing']) }}</td>
                                    <td class="cell-muted">{{ $row['note'] !== '' ? $row['note'] : '-' }}</td>
                                    @if(!empty($actions['can_pack']))
                                        <td class="num">
                                            <input type="checkbox" class="confirm-item" value="{{ $row['id'] }}"
                                                   style="width: 18px; height: 18px;">
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        @if(!empty($actions['can_pack']))
            <div class="action-bar">
                <div class="note">
                    <b>Kerjakan DO.</b> Centang seluruh barang setelah dicek. Setelah disimpan, DO masuk tab Cetak SJ.
                </div>
                <form method="POST" action="{{ route('delivery_order.packed', ['id' => $header['id']]) }}" id="frmPacked"
                      onsubmit="if (!confirm('Simpan hasil pemeriksaan? Seluruh barang harus sudah dicentang.')) { return false; } this.querySelector('button').disabled = true; this.querySelector('button').textContent = 'Memproses...'; return true;">
                    @csrf
                    <input type="hidden" name="from" value="{{ $backQuery }}">
                    <button type="submit" class="btn-acc" id="btnPacked" disabled>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                        Save (Kerjakan)
                    </button>
                </form>
            </div>
        @endif

        @if(!empty($actions['can_deliver']) || !empty($actions['deliver_blocked']) || in_array($status, ['DELIVERING', 'DELIVERED'], true))
            @php
                // Nudge: ketika SJ + Berangkat tampil bersama (PACKED), tampilkan urutan langkahnya.
                $showSteps = !empty($actions['can_deliver']);
            @endphp
            <div class="action-bar">
                @if(!empty($actions['can_deliver']))
                    <div class="note">
                        <b>Surat jalan dulu.</b> Pastikan SJ (langkah 1) sudah tercetak sebelum DO diberangkatkan (langkah 2). Stok fisik dipotong saat DO berangkat.
                    </div>
                @elseif(!empty($actions['deliver_blocked']))
                    <div class="note warn">
                        {{ $actions['deliver_blocked'] }} <b>Surat jalan</b> tetap bisa dicetak.
                    </div>
                @else
                    <div class="note">
                        <b>Surat Jalan.</b> Cetak ulang surat jalan DO ini.
                    </div>
                @endif
                <div class="action-buttons">
                    @if(in_array($status, ['PACKED', 'DELIVERING', 'DELIVERED'], true))
                        <a href="{{ route('delivery_order.print_sj', ['id' => $header['id'], 'from' => $backQuery]) }}"
                           target="_blank" rel="noopener" class="btn-line">
                            @if($showSteps)<span class="step-num">1</span>@endif
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 9V3h12v6"/><path d="M6 18H4a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2h-2"/><rect x="6" y="14" width="12" height="8"/></svg>
                            Cetak SJ
                        </a>
                    @endif
                    @if(!empty($actions['can_deliver']))
                        <form method="POST" action="{{ route('delivery_order.sending', ['id' => $header['id']]) }}"
                              onsubmit="if (!confirm('Langkah 2.\nPastikan surat jalan DO {{ $header['do_code'] }} (langkah 1) sudah tercetak terlebih dahulu.\n\nBerangkatkan DO? Stok fisik akan dipotong.')) { return false; } this.querySelector('button').disabled = true; this.querySelector('button').textContent = 'Memproses...'; return true;">
                            @csrf
                            <input type="hidden" name="from" value="{{ $backQuery }}">
                            <button type="submit" class="btn-acc">
                                <span class="step-num">2</span>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14"/><path d="M13 6l6 6-6 6"/></svg>
                                DELIVERING / BERANGKAT
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        @if($status === 'DELIVERING')
            <section class="panel detail-panel" style="margin-top:12px;">
                <header class="panel-head">
                    <div>
                        <h2>Update Resi</h2>
                        <small>Upload bukti kirim dan isi resi ekspedisi</small>
                    </div>
                </header>

                <div style="padding: 16px;">
                    @if(!empty($actions['sent_blocked']))
                        <div class="error-note" style="margin-bottom: 14px;">
                            <span>{{ $actions['sent_blocked'] }}</span>
                        </div>
                    @endif

                    @if(!empty($header['image_url']) || !empty($header['image2_url']))
                        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:16px;">
                            @if(!empty($header['image_url']))
                                <a href="{{ $header['image_url'] }}" target="_blank">
                                    <img src="{{ $header['image_url'] }}" style="max-width:180px; max-height:180px; border-radius:10px; border:1px solid #e6eeeb;">
                                </a>
                            @endif
                            @if(!empty($header['image2_url']))
                                <a href="{{ $header['image2_url'] }}" target="_blank">
                                    <img src="{{ $header['image2_url'] }}" style="max-width:180px; max-height:180px; border-radius:10px; border:1px solid #e6eeeb;">
                                </a>
                            @endif
                        </div>
                    @endif

                    <form method="POST" action="{{ route('delivery_order.sent', ['id' => $header['id']]) }}"
                          enctype="multipart/form-data"
                          onsubmit="if (!confirm('Selesaikan DO {{ $header['do_code'] }} dan simpan resi?')) { return false; } this.querySelector('button').disabled = true; this.querySelector('button').textContent = 'Memproses...'; return true;">
                        @csrf
                        <input type="hidden" name="from" value="{{ $backQuery }}">

                        <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(210px,1fr)); gap:14px;">
                            <div class="field">
                                <label for="image">Foto 1</label>
                                <input type="file" id="image" name="image" accept="image/png,image/jpeg" {{ !empty($actions['can_sent']) ? '' : 'disabled' }}>
                            </div>
                            <div class="field">
                                <label for="image2">Foto 2</label>
                                <input type="file" id="image2" name="image2" accept="image/png,image/jpeg" {{ !empty($actions['can_sent']) ? '' : 'disabled' }}>
                            </div>
                            <div class="field">
                                <label for="delivery_cost_idr">Ongkir (IDR)</label>
                                <input type="text" id="delivery_cost_idr" value="{{ $idr($cost['delivery_cost_idr'] ?? 0) }}" readonly>
                            </div>
                            <div class="field">
                                <label for="other_cost_note">Resi - Ekspedisi</label>
                                <input list="ekspedisi-list" id="other_cost_note" name="other_cost_note"
                                       value="{{ $cost['other_cost_note'] ?? '' }}" placeholder="Pilih / tulis ekspedisi"
                                       {{ !empty($actions['can_sent']) ? '' : 'disabled' }}>
                                <datalist id="ekspedisi-list">
                                    @foreach($ekspedisi as $option)
                                        <option value="{{ $option['name'] ?? '' }}"></option>
                                    @endforeach
                                </datalist>
                            </div>
                            <div class="field">
                                <label for="other_cost_idr">Resi (IDR) - Nominal</label>
                                <input type="number" step="any" min="0" id="other_cost_idr" name="other_cost_idr"
                                       value="{{ (float) ($cost['other_cost_idr'] ?? 0) }}"
                                       {{ !empty($actions['can_sent']) ? '' : 'disabled' }}>
                            </div>
                        </div>

                        @if(!empty($actions['can_sent']))
                            <div style="margin-top:16px;">
                                <button type="submit" class="btn-acc">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                                    Selesaikan (Delivered)
                                </button>
                            </div>
                        @endif
                    </form>
                </div>
            </section>
        @endif

        @if($status === 'DELIVERED')
            <section class="panel detail-panel" style="margin-top:12px;">
                <header class="panel-head">
                    <div>
                        <h2>History Resi</h2>
                        <small>Biaya pengiriman & bukti kirim</small>
                    </div>
                </header>
                <div style="padding: 16px;">
                    <div class="meta-grid" style="margin-top:0; padding-top:0; border-top:0;">
                        <div class="meta-item">
                            <span>Ongkir (Note)</span>
                            <b>{{ $cost['delivery_cost_note'] ?: '-' }}</b>
                        </div>
                        <div class="meta-item">
                            <span>Ongkir (IDR)</span>
                            <b>Rp {{ $idr($cost['delivery_cost_idr'] ?? 0) }}</b>
                        </div>
                        <div class="meta-item">
                            <span>Resi (Ekspedisi)</span>
                            <b>{{ $cost['other_cost_note'] ?: '-' }}</b>
                        </div>
                        <div class="meta-item">
                            <span>Resi (IDR)</span>
                            <b>Rp {{ $idr($cost['other_cost_idr'] ?? 0) }}</b>
                        </div>
                        <div class="meta-item">
                            <span>Grand Total</span>
                            <b>Rp {{ $idr($cost['grand_total_idr'] ?? 0) }}</b>
                        </div>
                        <div class="meta-item">
                            <span>Tanggal Dikirim</span>
                            <b>{{ $header['date_sent'] }}</b>
                        </div>
                    </div>

                    @if(!empty($header['image_url']) || !empty($header['image2_url']))
                        <div style="display:flex; gap:12px; flex-wrap:wrap; margin-top:16px;">
                            @if(!empty($header['image_url']))
                                <a href="{{ $header['image_url'] }}" target="_blank">
                                    <img src="{{ $header['image_url'] }}" style="max-width:200px; max-height:200px; border-radius:10px; border:1px solid #e6eeeb;">
                                </a>
                            @endif
                            @if(!empty($header['image2_url']))
                                <a href="{{ $header['image2_url'] }}" target="_blank">
                                    <img src="{{ $header['image2_url'] }}" style="max-width:200px; max-height:200px; border-radius:10px; border:1px solid #e6eeeb;">
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </section>
        @endif
    @endif

    <script type="text/javascript">
        (function () {
            var all = document.getElementById('check-all-items');
            var items = Array.prototype.slice.call(document.querySelectorAll('.confirm-item'));
            var save = document.getElementById('btnPacked');

            function sync() {
                if (!save) {
                    return;
                }
                var checked = items.filter(function (el) { return el.checked; }).length;
                save.disabled = items.length === 0 || checked !== items.length;
            }

            if (all) {
                all.addEventListener('change', function () {
                    items.forEach(function (el) { el.checked = all.checked; });
                    sync();
                });
            }

            items.forEach(function (el) {
                el.addEventListener('change', sync);
            });

            sync();
        })();
    </script>
@endsection

