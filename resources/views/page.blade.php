@extends('layouts.app')

@section('title', $menu['label'])

@section('content')
    <div class="breadcrumb">
        <a href="{{ route('dashboard') }}">Beranda</a> / {{ $menu['label'] }}
    </div>

    <div class="panel">
        <h2>{{ $menu['label'] }}</h2>
        <p>{{ $menu['description'] }}</p>

        <div class="notice">
            Halaman ini belum aktif. Fitur akan diisi pada fase {{ $menu['phase'] }} sesuai rencana
            pemisahan WMS. Sementara itu gunakan menu yang sama di Trans.
        </div>
    </div>
@endsection
