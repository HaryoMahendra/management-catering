@extends('master')

@section('title', 'Barcode Karyawan')

@section('content')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<style>
    .catering-card {
        background: #ffffff;
        border-radius: 14px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
        border: 1px solid #e6e9ef;
        transition: transform 0.15s;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .catering-card:hover {
        transform: translateY(-2px);
    }

    .card-header-navy {
        background-color: #0b1f41;
        color: #ffffff;
        padding: 10px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-header-navy h4 {
        margin: 0;
        font-weight: 800;
        letter-spacing: 1px;
        font-size: 1rem;
    }

    .card-body-custom {
        padding: 14px 16px;
        flex: 1;
    }

    .info-row {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
        font-size: 0.85rem;
    }

    .info-label {
        font-weight: 600;
        color: #6b7280;
        width: 80px;
        font-size: 0.82rem;
        flex-shrink: 0;
    }

    .info-value {
        font-weight: 700;
        color: #111111;
        font-size: 0.85rem;
    }

    .scan-instruction {
        display: flex;
        align-items: center;
        margin-top: 10px;
    }

    .icon-circle-navy {
        width: 30px;
        height: 30px;
        background-color: #0b1f41;
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 10px;
        flex-shrink: 0;
        font-size: 0.75rem;
    }

    .instruction-text {
        font-size: 0.72rem;
        font-weight: 600;
        color: #444444;
        line-height: 1.2;
    }

    .qr-container {
        position: relative;
        padding: 8px;
        display: inline-block;
    }

    .qr-container img {
        max-width: 100%;
        height: auto;
        display: block;
    }

    .qr-container::before,
    .qr-container::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        border-color: #0b1f41;
        border-style: solid;
    }

    .qr-container::before {
        top: 0;
        left: 0;
        border-width: 3px 0 0 3px;
        border-top-left-radius: 6px;
    }

    .qr-container::after {
        bottom: 0;
        right: 0;
        border-width: 0 3px 3px 0;
        border-bottom-right-radius: 6px;
    }

    .qr-corners-alt {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        pointer-events: none;
    }

    .qr-corners-alt::before,
    .qr-corners-alt::after {
        content: '';
        position: absolute;
        width: 20px;
        height: 20px;
        border-color: #0b1f41;
        border-style: solid;
    }

    .qr-corners-alt::before {
        top: 0;
        right: 0;
        border-width: 3px 3px 0 0;
        border-top-right-radius: 6px;
    }

    .qr-corners-alt::after {
        bottom: 0;
        left: 0;
        border-width: 0 0 3px 3px;
        border-bottom-left-radius: 6px;
    }

    .card-footer-soft {
        background-color: #eef4ff;
        padding: 8px 16px;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 6px;
        border-top: 1px solid #dce6f7;
    }

    .footer-note {
        display: flex;
        align-items: center;
        font-size: 0.68rem;
        font-weight: 600;
        color: #0b1f41;
    }

    .icon-circle-check {
        width: 18px;
        height: 18px;
        background-color: #0b1f41;
        color: #ffffff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.55rem;
        margin-right: 6px;
        flex-shrink: 0;
    }

    .company-logo-text {
        font-weight: 800;
        color: #0b1f41;
        font-size: 0.72rem;
        letter-spacing: 0.3px;
        display: flex;
        align-items: center;
        white-space: nowrap;
    }

    /* ==== Style Checkbox Seleksi ==== */
    .card-select-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f1f4fa;
        border: 1px solid #e6e9ef;
        border-bottom: none;
        border-radius: 10px 10px 0 0;
        padding: 6px 12px;
        font-size: 0.78rem;
        font-weight: 600;
        color: #0b1f41;
    }

    .card-select-bar label {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
    }

    .card-wrapper.card-hidden-filter {
        display: none !important;
    }

    .card-wrapper.card-unchecked .catering-card {
        opacity: 0.45;
        filter: grayscale(40%);
    }

    @media print {
        @page {
            size: A4 portrait;
            margin: 8mm;
        }

        .no-print {
            display: none !important;
        }

        body * {
            visibility: hidden !important;
        }

        #printArea,
        #printArea * {
            visibility: visible !important;
        }

        #printArea {
            position: absolute !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
        }

        body {
            background-color: #ffffff !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .container,
        .container-fluid {
            max-width: 100% !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .row {
            display: flex !important;
            flex-wrap: wrap !important;
            margin: 0 !important;
        }

        .col-lg-4,
        .col-md-6,
        .col-sm-12 {
            flex: 0 0 50% !important;
            max-width: 50% !important;
            padding: 3mm !important;
            box-sizing: border-box !important;
        }

        .card-wrapper.card-unchecked,
        .card-wrapper.card-hidden-filter {
            display: none !important;
        }

        .catering-card {
            box-shadow: none !important;
            border: 1px solid #111111 !important;
            page-break-inside: avoid !important;
            width: 8.56cm !important;
            height: 5.4cm !important;
            margin: 0 auto !important;
            border-radius: 6px !important;
            display: flex !important;
            flex-direction: column !important;
            justify-content: space-between !important;
            opacity: 1 !important;
            filter: none !important;
        }

        .card-header-navy {
            background-color: #0b1f41 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            padding: 5px 12px !important;
            height: 0.95cm !important;
        }

        .card-header-navy h4 {
            font-size: 0.92rem !important;
            letter-spacing: 0.5px !important;
        }

        .card-body-custom {
            padding: 8px 12px !important;
            flex-grow: 1 !important;
            display: flex !important;
            align-items: center !important;
        }

        .info-row {
            padding-bottom: 2px !important;
            margin-bottom: 4px !important;
        }

        .info-label {
            width: 72px !important;
            font-size: 0.76rem !important;
        }

        .info-value {
            font-size: 0.8rem !important;
        }

        .scan-instruction {
            margin-top: 5px !important;
        }

        .icon-circle-navy {
            width: 26px !important;
            height: 26px !important;
            font-size: 0.68rem !important;
            margin-right: 8px !important;
        }

        .instruction-text {
            font-size: 0.62rem !important;
        }

        .qr-container {
            padding: 4px !important;
        }

        .qr-container img {
            width: 2.8cm !important;
            height: 2.8cm !important;
        }

        .qr-container::before,
        .qr-container::after,
        .qr-corners-alt::before,
        .qr-corners-alt::after {
            width: 10px !important;
            height: 10px !important;
            border-width: 2px !important;
        }

        .card-footer-soft {
            background-color: #eef4ff !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            padding: 5px 12px !important;
            height: 0.85cm !important;
        }

        .footer-note {
            font-size: 0.58rem !important;
        }

        .icon-circle-check {
            width: 14px !important;
            height: 14px !important;
            font-size: 0.45rem !important;
            margin-right: 5px !important;
        }

        .company-logo-text {
            font-size: 0.62rem !important;
        }
    }
</style>

<div class="container-fluid my-4" id="printArea">
    <!-- Header Halaman -->
    <div class="mb-4 no-print">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Cetak Kartu Catering Karyawan</h1>
    </div>

    <!-- CARD FILTER DATA KARTU KARYAWAN (Tampilan Sama Seperti Booking Catering) -->
    <div class="card shadow mb-4 border-0 no-print">
        <div class="card-header py-3 bg-light border-bottom d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                Filter Data Cetak Kartu Karyawan
            </h6>

            <!-- Tombol Cetak & Counter Kartu Terpilih -->
            <div class="d-flex align-items-center">
                <span class="font-weight-bold text-dark small mr-3">
                    <span id="selectedCount" class="badge badge-primary px-2 py-1">0</span> Kartu Terpilih
                </span>
                <button onclick="printSelectedCards()" class="btn btn-primary btn-sm font-weight-bold shadow-sm">
                    <i class="fas fa-print fa-sm text-white-50 mr-2"></i> Cetak Kartu Terpilih
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="row align-items-end">
                <!-- Cari Nama / NIF -->
                <div class="col-lg-3 col-md-6 mb-3">
                    <label class="font-weight-bold text-dark small mb-1">Cari Karyawan / NIF</label>
                    <input type="text" id="cardSearchInput" class="form-control font-weight-bold" placeholder="Nama / NIF..." oninput="applyCardFilter()">
                </div>

                <!-- Filter Departemen -->
                <div class="col-lg-2 col-md-6 mb-3">
                    <label class="font-weight-bold text-dark small mb-1">Departemen</label>
                    <select id="departemenFilter" class="form-control font-weight-bold" onchange="applyCardFilter()">
                        <option value="">-- Semua --</option>
                        @foreach($karyawans->pluck('departemen')->filter()->unique()->sort() as $dept)
                            <option value="{{ $dept }}">{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Filter Shift -->
                <div class="col-lg-2 col-md-6 mb-3">
                    <label class="font-weight-bold text-dark small mb-1">Shift</label>
                    <select id="shiftFilter" class="form-control font-weight-bold" onchange="applyCardFilter()">
                        <option value="">-- Semua --</option>
                        <option value="Shift 1">Shift 1</option>
                        <option value="Shift 2">Shift 2</option>
                    </select>
                </div>

                <!-- Filter Lokasi -->
                <div class="col-lg-2 col-md-6 mb-3">
                    <label class="font-weight-bold text-dark small mb-1">Lokasi</label>
                    <select id="lokasiFilter" class="form-control font-weight-bold" onchange="applyCardFilter()">
                        <option value="">-- Semua --</option>
                        <option value="Kantin 1">Kantin 1</option>
                        <option value="Kantin 2">Kantin 2</option>
                    </select>
                </div>

                <!-- Tombol Aksi Seleksi Cepat -->
                <div class="col-lg-3 col-md-12 mb-3 d-flex">
                    <button type="button" class="btn btn-outline-primary font-weight-bold flex-grow-1 mr-2 shadow-sm" onclick="toggleAllVisible(true)">
                        <i class="fas fa-check-square mr-1"></i> Pilih Semua
                    </button>
                    <button type="button" class="btn btn-outline-secondary font-weight-bold flex-grow-1 shadow-sm" onclick="toggleAllVisible(false)">
                        <i class="fas fa-square mr-1"></i> Batal Pilih
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Grid Daftar Kartu Karyawan -->
    <div class="row" id="cardsGrid">
        @forelse($karyawans as $item)
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4 card-wrapper"
                 data-nama="{{ strtolower($item->nama) }}"
                 data-nif="{{ strtolower($item->nif) }}"
                 data-departemen="{{ strtolower($item->departemen ?? '') }}"
                 data-lokasi="{{ strtolower($item->lokasi ?? '') }}"
                 data-shift="{{ strtolower($item->shift ?? '') }}">

                <!-- Bar Checkbox Seleksi (Tidak Ikut Tercetak) -->
                <div class="card-select-bar no-print">
                    <label>
                        <input type="checkbox" class="card-select-checkbox" checked onchange="onCardCheckboxChange(this)">
                        Cetak kartu ini
                    </label>
                </div>

                <div class="catering-card">

                    <!-- 1. Header Kartu -->
                    <div class="card-header-navy">
                        <h4>CATERING CARD</h4>
                        <i class="fas fa-utensils fa-lg"></i>
                    </div>

                    <!-- 2. Body Kartu -->
                    <div class="card-body-custom">
                        <div class="row align-items-center no-gutters w-100">

                            <!-- Kolom Kiri: Detail Informasi -->
                            <div class="col-8 pr-1">
                                <div class="info-row">
                                    <span class="info-label">Nama</span>
                                    <span class="info-value">: {{ $item->nama }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">NIF / ID</span>
                                    <span class="info-value">: {{ $item->nif }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Departemen</span>
                                    <span class="info-value">: {{ $item->departemen ?? '-' }}</span>
                                </div>

                                <!-- Petunjuk Scan -->
                                <div class="scan-instruction">
                                    <div class="icon-circle-navy">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <div class="instruction-text">
                                        Scan barcode ini<br>untuk pengambilan catering
                                    </div>
                                </div>
                            </div>

                            <!-- Kolom Kanan: QR Code -->
                            <div class="col-4 text-center pl-1">
                                <div class="qr-container">
                                    <div class="qr-corners-alt"></div>
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($item->nif) }}"
                                        alt="QR {{ $item->nif }}" class="img-fluid p-1">
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- 3. Footer Kartu -->
                    <div class="card-footer-soft">
                        <div class="footer-note">
                            <div class="icon-circle-check">
                                <i class="fas fa-check"></i>
                            </div>
                            <span>Satu karyawan hanya dapat melakukan pengambilan catering 1 kali per hari.</span>
                        </div>
                        <div class="company-logo-text">
                            <i class="fas fa-cubes text-primary mr-1 fa-lg"></i>
                            <span>MANAGEMENT CATERING</span>
                        </div>
                    </div>

                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <div class="alert alert-warning">
                    Belum ada data karyawan terdaftar. Silakan upload data Excel terlebih dahulu!
                </div>
            </div>
        @endforelse
    </div>
</div>

<script>
    function updateSelectedCounter() {
        var total = document.querySelectorAll('.card-wrapper:not(.card-hidden-filter) .card-select-checkbox:checked').length;
        var counterEl = document.getElementById('selectedCount');
        if (counterEl) counterEl.textContent = total;
    }

    function onCardCheckboxChange(checkbox) {
        var wrapper = checkbox.closest('.card-wrapper');
        wrapper.classList.toggle('card-unchecked', !checkbox.checked);
        updateSelectedCounter();
    }

    function toggleAllVisible(state) {
        document.querySelectorAll('.card-wrapper:not(.card-hidden-filter)').forEach(function (wrapper) {
            var checkbox = wrapper.querySelector('.card-select-checkbox');
            if (checkbox) {
                checkbox.checked = state;
                wrapper.classList.toggle('card-unchecked', !state);
            }
        });
        updateSelectedCounter();
    }

    function applyCardFilter() {
        var keyword = document.getElementById('cardSearchInput').value.trim().toLowerCase();
        var dept = document.getElementById('departemenFilter').value.trim().toLowerCase();
        var lokasi = document.getElementById('lokasiFilter').value.trim().toLowerCase();
        var shift = document.getElementById('shiftFilter').value.trim().toLowerCase();

        document.querySelectorAll('.card-wrapper').forEach(function (wrapper) {
            var nama = wrapper.dataset.nama || '';
            var nif = wrapper.dataset.nif || '';
            var departemen = wrapper.dataset.departemen || '';
            var cardLokasi = wrapper.dataset.lokasi || '';
            var cardShift = wrapper.dataset.shift || '';

            var matchKeyword = !keyword || nama.includes(keyword) || nif.includes(keyword);
            var matchDept = !dept || departemen === dept;
            var matchLokasi = !lokasi || cardLokasi === lokasi;
            var matchShift = !shift || cardShift === shift;

            wrapper.classList.toggle('card-hidden-filter', !(matchKeyword && matchDept && matchLokasi && matchShift));
        });

        updateSelectedCounter();
    }

    function printSelectedCards() {
        var totalSelected = document.querySelectorAll('.card-wrapper:not(.card-hidden-filter) .card-select-checkbox:checked').length;
        if (totalSelected === 0) {
            alert('Pilih minimal satu kartu karyawan sebelum mencetak.');
            return;
        }
        window.print();
    }

    document.addEventListener('DOMContentLoaded', function () {
        updateSelectedCounter();
    });
</script>

@endsection