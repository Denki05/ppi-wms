@extends('layouts.app')

@section('title', 'Notifikasi')

@push('styles')
@include('partials.list-styles')
<style>
    .nlist { display: flex; flex-direction: column; }
    .nitem {
        display: flex; align-items: flex-start; gap: 12px;
        padding: 13px 16px; border-top: 1px solid #eef4f1;
        color: inherit; transition: background .12s ease;
    }
    .nitem:first-child { border-top: 0; }
    .nitem:hover { background: #f8fbfa; }
    .nicon {
        flex: none; width: 34px; height: 34px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        background: #eef2f7; color: #64748b;
    }
    .nicon svg { width: 16px; height: 16px; }
    .nitem[data-priority="action"] .nicon { background: #fff7e6; color: #b45309; }
    .nitem.is-unread .nicon { box-shadow: inset 0 0 0 1px rgba(15,118,110,.18); }
    .nbody { flex: 1; min-width: 0; }
    .ntitle { font-size: 13px; font-weight: 600; color: #0f172a; line-height: 1.35; }
    .nitem.is-unread .ntitle { font-weight: 700; }
    .nsub { margin-top: 2px; font-size: 11.5px; color: #64748b; }
    .nmeta { flex: none; text-align: right; font-size: 10.5px; color: #94a3b8; white-space: nowrap; }
    .ndot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; background: #0f766e; margin-left: 6px; vertical-align: middle; }
    .nitem[data-priority="action"].is-unread .ndot { background: #f59e0b; }
    @media (max-width: 620px) {
        .nitem { padding: 12px; }
        .nmeta { font-size: 10px; }
    }
</style>
@endpush

@section('content')
    @php
        $items = $data['items'] ?? [];
        $unread = (int) ($data['unread'] ?? 0);
        $total = (int) ($data['total'] ?? 0);
        $page = (int) ($data['page'] ?? 1);
        $lastPage = (int) ($data['last_page'] ?? 1);
        $pageStart = max(1, min($page - 2, $lastPage - 4));
        $pageEnd = min($lastPage, $pageStart + 4);
        $titleFor = function (array $row) {
            $status = (int) ($row['do_status'] ?? 0);
            if ($status === 3) {
                return 'DO ' . ($row['code'] ?? '-') . ' siap dikerjakan';
            }
            if ($status === 6) {
                return 'DO ' . ($row['code'] ?? '-') . ' selesai dikirim';
            }
            return 'DO ' . ($row['code'] ?? '-') . ' diperbarui';
        };
    @endphp

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

    <div class="dhead">
        <div>
            <h1>Notifikasi</h1>
            <p>Pemberitahuan Delivery Order dari Trans</p>
        </div>
        <div class="dhead-side">
            <span>{{ $unread > 0 ? $unread . ' belum dibaca' : 'Semua sudah dibaca' }}</span>
            <a href="{{ route('notifications', array_filter(['filter' => $filter === 'unread' ? 'unread' : null])) }}" class="icon-btn" title="Muat ulang" aria-label="Muat ulang">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 3v6h-6"/></svg>
            </a>
        </div>
    </div>

    <div class="toolbar">
        <nav class="tabs">
            <a href="{{ route('notifications') }}" class="tab {{ $filter === 'all' ? 'active' : '' }}">
                Semua
                <span class="n">{{ $filter === 'all' ? $total : '' }}</span>
            </a>
            <a href="{{ route('notifications', ['filter' => 'unread']) }}" class="tab {{ $filter === 'unread' ? 'active' : '' }}">
                Belum dibaca
                <span class="n">{{ $unread }}</span>
            </a>
        </nav>

        @if($unread > 0)
            <form method="POST" action="{{ route('notifications.read_all') }}" style="margin-left:auto;">
                @csrf
                <button type="submit" class="btn">Tandai semua dibaca</button>
            </form>
        @endif
    </div>

    @if(!$data)
        <div class="error-note">
            <span>{{ $errorMessage ?? 'Notifikasi belum bisa dimuat.' }}</span>
            <a href="{{ route('notifications') }}">Coba lagi</a>
        </div>
    @else
        <section class="panel">
            <header class="panel-head">
                <div>
                    <h2>{{ $filter === 'unread' ? 'Belum dibaca' : 'Semua notifikasi' }}</h2>
                    <small>{{ $total }} notifikasi</small>
                </div>
                <span class="count-pill">{{ $unread }} unread</span>
            </header>

            @if(empty($items))
                <div class="empty">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.7 21a2 2 0 0 1-3.4 0"/></svg>
                    <span>
                        <b>Tidak ada notifikasi</b>
                        <small>{{ $filter === 'unread' ? 'Semua notifikasi sudah dibaca.' : 'Belum ada pemberitahuan DO.' }}</small>
                    </span>
                </div>
            @else
                <div class="nlist">
                    @foreach($items as $row)
                        <a href="{{ route('notifications.read', array_filter(['id' => $row['id'], 'do' => $row['do_id']])) }}"
                           class="nitem {{ !empty($row['is_unread']) ? 'is-unread' : '' }}"
                           data-priority="{{ $row['priority'] ?? 'info' }}">
                            <span class="nicon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 3h15v13H1z"/><path d="M16 8h4l3 3v5h-7V8z"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>
                            </span>
                            <span class="nbody">
                                <span class="ntitle">{{ $titleFor($row) }}</span>
                                <span class="nsub">
                                    {{ $row['customer'] ?: '-' }}{{ !empty($row['city']) ? ' · ' . $row['city'] : '' }}
                                </span>
                            </span>
                            <span class="nmeta">
                                {{ $row['human'] ?? '' }}
                                @if(!empty($row['is_unread']))
                                    <span class="ndot" aria-label="Belum dibaca"></span>
                                @endif
                            </span>
                        </a>
                    @endforeach
                </div>
            @endif

            @if($lastPage > 1)
                <div class="pager">
                    <span>Halaman {{ $page }} dari {{ $lastPage }} · {{ $total }} notifikasi</span>
                    <div class="links">
                        @if($page > 1)
                            <a href="{{ route('notifications', array_filter(['filter' => $filter === 'unread' ? 'unread' : null, 'page' => $page - 1])) }}">Sebelumnya</a>
                        @endif
                        @for($i = $pageStart; $i <= $pageEnd; $i++)
                            <a href="{{ route('notifications', array_filter(['filter' => $filter === 'unread' ? 'unread' : null, 'page' => $i])) }}"
                               class="{{ $i === $page ? 'active' : '' }}">{{ $i }}</a>
                        @endfor
                        @if($page < $lastPage)
                            <a href="{{ route('notifications', array_filter(['filter' => $filter === 'unread' ? 'unread' : null, 'page' => $page + 1])) }}">Berikutnya</a>
                        @endif
                    </div>
                </div>
            @endif
        </section>
    @endif
@endsection
