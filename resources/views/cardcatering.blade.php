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
            justify-content: space-between;
        }

        .catering-card:hover {
            transform: translateY(-2px);
        }

        .card-header-navy {
            background-color: #0b1f41;
            color: #ffffff;
            padding: 12px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header-navy h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: 1px;
            font-size: 1.05rem;
        }

        .card-body-custom {
            padding: 16px 18px;
            flex-grow: 1;
            display: flex;
            align-items: center;
        }

        .info-row {
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            font-size: 0.88rem;
        }

        .info-label {
            font-weight: 600;
            color: #6b7280;
            width: 85px;
            font-size: 0.85rem;
            flex-shrink: 0;
        }

        .info-value {
            font-weight: 700;
            color: #111111;
            font-size: 0.88rem;
        }

        .scan-instruction {
            display: flex;
            align-items: center;
            margin-top: 12px;
        }

        .icon-circle-navy {
            width: 32px;
            height: 32px;
            background-color: #0b1f41;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            flex-shrink: 0;
            font-size: 0.8rem;
        }

        .instruction-text {
            font-size: 0.72rem;
            font-weight: 600;
            color: #444444;
            line-height: 1.25;
        }

        .qr-container {
            position: relative;
            padding: 8px;
            display: inline-block;
        }

        .qr-container img {
            width: 100%;
            max-width: 110px;
            height: auto;
            display: block;
        }

        .qr-container::before,
        .qr-container::after {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            border-color: #0b1f41;
            border-style: solid;
        }

        .qr-container::before {
            top: 0;
            left: 0;
            border-width: 3px 0 0 3px;
            border-top-left-radius: 5px;
        }

        .qr-container::after {
            bottom: 0;
            right: 0;
            border-width: 0 3px 3px 0;
            border-bottom-right-radius: 5px;
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
            width: 18px;
            height: 18px;
            border-color: #0b1f41;
            border-style: solid;
        }

        .qr-corners-alt::before {
            top: 0;
            right: 0;
            border-width: 3px 3px 0 0;
            border-top-right-radius: 5px;
        }

        .qr-corners-alt::after {
            bottom: 0;
            left: 0;
            border-width: 0 0 3px 3px;
            border-bottom-left-radius: 5px;
        }

        .card-footer-soft {
            background-color: #eef4ff;
            padding: 10px 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            border-top: 1px solid #dce6f7;
        }

        .footer-note {
            display: flex;
            align-items: center;
            font-size: 0.68rem;
            font-weight: 600;
            color: #0b1f41;
            line-height: 1.2;
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
            cursor: pointer;
        }

        .card-wrapper.card-hidden-filter {
            display: none !important;
        }

        .card-wrapper.card-unchecked .catering-card {
            opacity: 0.45;
            filter: grayscale(40%);
        }

        /* ======================================================= */
        /* 2. MODE CETAK / PRINT PRESISI PERSIS TAMPILAN SISTEM    */
        /* ======================================================= */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
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
                margin: 0 !important;
                padding: 0 !important;
            }

            body {
                background-color: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .container-fluid {
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .row {
                display: flex !important;
                flex-wrap: wrap !important;
                margin: 0 !important;
            }

            /* 2 Kolom Kartu per baris di kertas A4 */
            .card-wrapper {
                flex: 0 0 50% !important;
                max-width: 50% !important;
                padding: 6mm 4mm !important;
                box-sizing: border-box !important;
                page-break-inside: avoid !important;
            }

            .card-wrapper.card-unchecked,
            .card-wrapper.card-hidden-filter {
                display: none !important;
            }

            /* Kartu mempertahankan rasio alami tanpa dipotong */
            .catering-card {
                box-shadow: none !important;
                border: 1.5px solid #0b1f41 !important;
                border-radius: 10px !important;
                height: auto !important;
                min-height: auto !important;
                width: 100% !important;
                opacity: 1 !important;
                filter: none !important;
                page-break-inside: avoid !important;
            }

            .card-header-navy {
                background-color: #0b1f41 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .card-footer-soft {
                background-color: #eef4ff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .icon-circle-navy,
            .icon-circle-check {
                background-color: #0b1f41 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

    <div class="container-fluid my-4" id="printArea">
        <div class="mb-4 no-print">
            <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Kartu Catering Karyawan</h1>
        </div>

        <div class="card shadow mb-4 border-0 no-print">
            <div class="card-header py-3 bg-light border-bottom d-flex align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="fas fa-solid fa-filter"></i>Filter Data Kartu Karyawan
                </h6>

                <div class="d-flex align-items-center">
                    <span class="font-weight-bold text-dark small mr-3">
                        <span id="selectedCount" class="badge badge-primary px-2 py-1">0</span> Kartu Terpilih
                    </span>
                    <button onclick="printSelectedCards()" class="btn btn-primary btn-sm font-weight-bold shadow-sm">
                        Cetak Kartu
                    </button>
                </div>
            </div>

            <div class="card-body">
                <div class="row align-items-end">

                    <div class="col-lg-3 col-md-5 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Cari Karyawan / NIK</label>
                        <input type="text" id="cardSearchInput" class="form-control font-weight-bold"
                            placeholder="Nama / NIK..." oninput="applyCardFilter()">
                    </div>


                    <div class="col-lg-3 col-md-4 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Departemen</label>
                        <select id="departemenFilter" class="form-control font-weight-bold" onchange="applyCardFilter()">
                            <option value="">-- Semua --</option>
                            @foreach ($karyawans->pluck('departemen')->filter()->unique()->sort() as $dept)
                                <option value="{{ $dept }}">{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>


                    <div class="col-lg-2 col-md-3 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Shift</label>
                        <select id="shiftFilter" class="form-control font-weight-bold" onchange="applyCardFilter()">
                            <option value="">-- Semua --</option>
                            <option value="Non Shift">Non Shift</option>
                            <option value="Shift 1">Shift 1</option>
                            <option value="Shift 2">Shift 2</option>
                            <option value="Shift 3">Shift 3</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Lokasi</label>
                        <select id="lokasiFilter" class="form-control font-weight-bold" onchange="applyCardFilter()">
                            <option value="">-- Semua --</option>
                            <option value="GCPI 1">GCPI 1</option>
                            <option value="GCPI 2">GCPI 2</option>
                            <option value="GCPI 3">GCPI 3</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-6 mb-3 d-flex">
                        <button type="button" class="btn btn-outline-success font-weight-bold flex-grow-1 mr-2 shadow-sm"
                            onclick="toggleAllVisible(true)" title="Pilih Semua Kartu">
                            <i class="fa-solid fa-circle-check fa-lg"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger font-weight-bold flex-grow-1 shadow-sm"
                            onclick="toggleAllVisible(false)" title="Batal Pilih Semua">
                            <i class="fa-solid fa-circle-xmark fa-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="cardsGrid">
            @forelse($karyawans as $item)
                <div class="col-lg-4 col-md-6 col-sm-12 mb-5 card-wrapper" data-nama="{{ strtolower($item->nama) }}"
                    data-nif="{{ strtolower($item->nif) }}" data-departemen="{{ strtolower($item->departemen ?? '') }}"
                    data-lokasi="{{ strtolower($item->lokasi ?? '') }}" data-shift="{{ strtolower($item->shift ?? '') }}">

                    <div class="card-select-bar no-print">
                        <label>
                            <input type="checkbox" class="card-select-checkbox" checked
                                onchange="onCardCheckboxChange(this)">
                            Cetak kartu
                        </label>
                    </div>

                    <div class="catering-card">

                        <div class="card-header-navy">
                            <h4 class="m-0 font-weight-bold">CATERING CARD</h4>
                            <div class="logo d-flex align-items-center">
                                <img src="{{ asset('logogodrej.png') }}" alt="Logo" class="img-fluid"
                                    style="max-height: 30px; width: auto;">
                            </div>
                        </div>

                        <div class="card-body-custom">
                            <div class="row align-items-center no-gutters w-100">

                                <div class="col-8 pr-1">
                                    <div class="info-row">
                                        <span class="info-label">Nama</span>
                                        <span class="info-value">: {{ $item->nama }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">NIK </span>
                                        <span class="info-value">: {{ $item->nif }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Departemen</span>
                                        <span class="info-value">: {{ $item->departemen ?? '-' }}</span>
                                    </div>

                                    <div class="scan-instruction">
                                        <div class="icon-circle-navy">
                                            <i class="fas fa-qrcode"></i>
                                        </div>
                                        <div class="instruction-text">
                                            Scan barcode ini<br>untuk pengambilan catering
                                        </div>
                                    </div>
                                </div>

                                <div class="col-4 text-center pl-1">
                                    <div class="qr-container">
                                        <div class="qr-corners-alt"></div>
                                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($item->nif) }}"
                                            alt="QR {{ $item->nif }}" class="img-fluid p-1">
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="card-footer-soft">
                            <div class="footer-note">
                                <div class="icon-circle-check">
                                    <i class="fas fa-check"></i>
                                </div>
                                <span>Satu karyawan hanya dapat melakukan pengambilan catering 1 kali per hari.</span>
                            </div>
                            <div class="logo">
                                <img src="{{ asset('logo.png') }}" alt="Logo" class="img-fluid"
                                    style="max-height: 28px;">
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
            var total = document.querySelectorAll('.card-wrapper:not(.card-hidden-filter) .card-select-checkbox:checked')
                .length;
            var counterEl = document.getElementById('selectedCount');
            if (counterEl) counterEl.textContent = total;
        }

        function onCardCheckboxChange(checkbox) {
            var wrapper = checkbox.closest('.card-wrapper');
            wrapper.classList.toggle('card-unchecked', !checkbox.checked);
            updateSelectedCounter();
        }

        function toggleAllVisible(state) {
            document.querySelectorAll('.card-wrapper:not(.card-hidden-filter)').forEach(function(wrapper) {
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

            document.querySelectorAll('.card-wrapper').forEach(function(wrapper) {
                var nama = wrapper.dataset.nama || '';
                var nif = wrapper.dataset.nif || '';
                var departemen = wrapper.dataset.departemen || '';
                var cardLokasi = wrapper.dataset.lokasi || '';
                var cardShift = wrapper.dataset.shift || '';

                var matchKeyword = !keyword || nama.includes(keyword) || nif.includes(keyword);
                var matchDept = !dept || departemen === dept;
                var matchLokasi = !lokasi || cardLokasi === lokasi;
                var matchShift = !shift || cardShift === shift;

                wrapper.classList.toggle('card-hidden-filter', !(matchKeyword && matchDept && matchLokasi &&
                    matchShift));
            });

            updateSelectedCounter();
        }

        function printSelectedCards() {
            var totalSelected = document.querySelectorAll(
                '.card-wrapper:not(.card-hidden-filter) .card-select-checkbox:checked').length;
            if (totalSelected === 0) {
                alert('Pilih minimal satu kartu karyawan sebelum mencetak.');
                return;
            }
            window.print();
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateSelectedCounter();
        });
    </script>

@endsection
