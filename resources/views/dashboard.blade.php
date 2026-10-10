@extends('master')

@section('title', 'Dashboard')

@section('content')
    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">
        Selamat Datang 
        @if(Auth::check())
            <span class="font-weight-bold text-gray-800">{{ Auth::user()->name }}</span>

        @else
            Guest
        @endif
        di Management Catering
    </h1>

    <!-- Panggil Card Langsung (tanpa pembungkus row ganda) -->
    @include('layouts.card')

    <!-- Panggil Tabel Monitoring -->
    @include('layouts.table')
@endsection