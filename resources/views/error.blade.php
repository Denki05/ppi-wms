@extends('layouts.app')

@section('title', 'Login Gagal')

@section('content')
    <div class="error-box">
        <h1>Login WMS Gagal</h1>
        <p>{{ $message }}</p>
        <a href="{{ config('wms.trans_portal_url') }}" class="btn">Kembali ke Portal Trans</a>
    </div>
@endsection
