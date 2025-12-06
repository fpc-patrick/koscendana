@extends('template.main')
@section('title', 'Dashboard')
@section('content')

<div class="content-wrapper">
    <!-- Content Header -->
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">@yield('title')</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/dashboard') }}">@yield('title')</a></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Main content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">

                {{-- Kamar --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3>{{ \App\Models\Kamar::count() }}</h3>
                            <p>Total Kamar</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-home"></i>
                        </div>
                        <a href="{{ route('kamars.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{-- Penghuni --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>{{ \App\Models\Penghuni::count() }}</h3>
                            <p>Total Penghuni</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person"></i>
                        </div>
                        <a href="{{ route('penghunis.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{-- Transaksi --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3>{{ \App\Models\Transaksi::count() }}</h3>
                            <p>Total Transaksi</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-cash"></i>
                        </div>
                        <a href="{{ route('transaksis.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{-- Tagihan --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3>{{ \App\Models\Tagihan::count() }}</h3>
                            <p>Total Tagihan</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pricetag"></i>
                        </div>
                        <a href="{{ route('tagihans.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{-- Barang --}}
                <div class="col-lg-3 col-6 mt-3">
                    <div class="small-box bg-primary">
                        <div class="inner">
                            <h3>{{ \App\Models\Barang::count() }}</h3>
                            <p>Tabel Pengeluaran</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="{{ route('barangs.index') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                {{-- Saldo Pemasukan --}}
                <div class="col-lg-3 col-6 mt-3">
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3>Count</h3>
                            <p>Saldo Pemasukan</p>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="{{ url('/pengaturansaldo') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>

                @php
                    $userEmail = auth()->user()->email;
                @endphp

                @if($userEmail == 'admin@gmail.com')
                    <div class="col-lg-3 col-6 mt-3">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3>User</h3>
                                <p>User Account</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-person-add"></i>
                            </div>
                            <a href="{{ url('/account') }}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

@endsection