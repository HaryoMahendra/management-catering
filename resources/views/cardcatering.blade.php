@extends('master')

@section('title', 'Barcode Karyawan')

@section('content')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Ukuran Presisi Standar ID Card CR80: 8.56cm x 5.4cm */
        .catering-card {
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.06);
            border: 1px solid #0b1f41;
            transition: transform 0.15s;
            width: 8.56cm;
            height: 5.4cm;
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            margin: 0 auto;
        }

        .catering-card:hover {
            transform: translateY(-2px);
        }

        .card-header-navy {
            background-color: #0b1f41;
            color: #ffffff;
            padding: 4px 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 0.75cm;
            box-sizing: border-box;
            flex-shrink: 0;
        }

        .card-header-navy h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: 0.5px;
            font-size: 0.75rem;
        }

        .card-body-custom {
            padding: 5px 10px;
            flex-grow: 1;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-sizing: border-box;
            overflow: hidden;
        }

        .card-info-area {
            flex: 1;
            min-width: 0;
            padding-right: 4px;
        }

        .card-qr-area {
            width: 72px;
            min-width: 72px;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .info-row {
            margin-bottom: 3px;
            display: flex;
            align-items: center;
            width: 100%;
            font-size: 0.52rem;
            line-height: 1.15;
            white-space: nowrap;
        }

        .info-label {
            font-weight: 700;
            color: #111111;
            width: 68px;
            min-width: 68px;
            font-size: 0.50rem;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
            min-width: 0;
            font-weight: 700;
            color: #111111;
            font-size: 0.54rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .scan-instruction {
            display: flex;
            align-items: center;
            margin-top: 20px;
            width: 100%;
            white-space: nowrap;
        }

        .icon-circle-navy {
            width: 16px;
            height: 16px;
            min-width: 16px;
            background-color: #0b1f41;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 4px;
            flex-shrink: 0;
            font-size: 0.55rem;
        }

        .instruction-text {
            font-size: 0.55rem;
            font-weight: 600;
            color: #444444;
            line-height: 1;
            white-space: nowrap;
        }

        .qr-container {
            position: relative;
            padding: 2px;
            display: inline-block;
        }

        .qr-container img {
            width: 1.8cm;
            height: 1.8cm;
            display: block;
        }

        .qr-container::before,
        .qr-container::after {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            border-color: #0b1f41;
            border-style: solid;
        }

        .qr-container::before {
            top: 0;
            left: 0;
            border-width: 2px 0 0 2px;
            border-top-left-radius: 2px;
        }

        .qr-container::after {
            bottom: 0;
            right: 0;
            border-width: 0 2px 2px 0;
            border-bottom-right-radius: 2px;
        }

        .qr-corners-alt::before,
        .qr-corners-alt::after {
            content: '';
            position: absolute;
            width: 8px;
            height: 8px;
            border-color: #0b1f41;
            border-style: solid;
        }

        .qr-corners-alt::before {
            top: 0;
            right: 0;
            border-width: 2px 2px 0 0;
            border-top-right-radius: 2px;
        }

        .qr-corners-alt::after {
            bottom: 0;
            left: 0;
            border-width: 0 0 2px 2px;
            border-bottom-left-radius: 2px;
        }

        .card-footer-soft {
            background-color: #eef4ff;
            padding: 3px 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 4px;
            border-top: 1px solid #dce6f7;
            height: 0.65cm;
            box-sizing: border-box;
            flex-shrink: 0;
        }

        .footer-note {
            display: flex;
            align-items: center;
            font-size: 0.45rem;
            font-weight: 600;
            color: #0b1f41;
            line-height: 1.1;
        }

        .icon-circle-check {
            width: 10px;
            height: 10px;
            background-color: #0b1f41;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.35rem;
            margin-right: 3px;
            flex-shrink: 0;
        }

        .card-select-bar {
            width: 8.56cm;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f1f4fa;
            border: 1px solid #e6e9ef;
            border-bottom: none;
            border-radius: 8px 8px 0 0;
            padding: 4px 10px;
            font-size: 0.75rem;
            font-weight: 600;
            color: #0b1f41;
        }

        .card-select-bar label {
            margin: 0;
            cursor: pointer;
        }

        .card-wrapper {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .card-wrapper.card-hidden-filter {
            display: none !important;
        }

        /* Kartu yang tidak berada di halaman aktif (pagination tampilan, TIDAK berlaku saat cetak/export) */
        .card-wrapper.card-hidden-page {
            display: none !important;
        }

        .card-wrapper.card-unchecked .catering-card {
            opacity: 0.45;
            filter: grayscale(40%);
        }

        /* FILTER TANGGAL - tombol reset kecil */
        .tanggal-filter-wrap {
            position: relative;
        }

        .btn-clear-tanggal {
            font-size: 0.7rem;
            font-weight: 700;
            color: #dc3545;
            background: none;
            border: none;
            padding: 2px 0 0;
            cursor: pointer;
        }

        .btn-clear-tanggal:hover {
            text-decoration: underline;
        }

        /* ==================== PAGINATION BAR (dirapikan) ==================== */
        .pagination-bar {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            margin: 1.5rem 0 2rem;
            padding: 14px 18px;
            background: #ffffff;
            border: 1px solid #e6e9ef;
            border-radius: 10px;
            box-shadow: 0 1px 6px rgba(0, 0, 0, 0.05);
        }

        .pagination-info {
            font-size: 0.82rem;
            font-weight: 700;
            color: #495057;
            text-align: center;
        }

        .pagination-controls {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            gap: 4px;
        }

        .pagination-controls .btn {
            border-radius: 6px;
        }

        #pageNumbers {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 4px;
            margin: 0 4px;
        }

        .page-btn {
            min-width: 36px;
            font-weight: 700;
        }

        .page-ellipsis {
            padding: 0 4px;
            color: #adb5bd;
            font-weight: 700;
        }

        /* MODE CETAK / PRINT */
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            html,
            body {
                width: 100% !important;
                height: auto !important;
                padding: 0 !important;
                margin: 0 !important;
                background: #ffffff !important;
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
                position: static !important;
                display: block !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            #cardsGrid {
                display: flex !important;
                flex-wrap: wrap !important;
                width: 100% !important;
                margin: 0 !important;
            }

            .card-wrapper {
                display: inline-block !important;
                width: 8.56cm !important;
                max-width: 8.56cm !important;
                height: auto !important;
                min-height: 5.4cm !important;
                margin: 0 0.3cm 0.3cm 0 !important;
                padding: 0 !important;
                vertical-align: top !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            /* Saat print, batasan "10 per halaman tampilan" diabaikan supaya semua kartu tercentang tetap tercetak */
            .card-wrapper.card-hidden-page {
                display: inline-block !important;
            }

            .card-wrapper.card-unchecked,
            .card-wrapper.card-hidden-filter {
                display: none !important;
            }

            .catering-card {
                box-shadow: none !important;
                border: 1px solid #0b1f41 !important;
                border-radius: 8px !important;
                width: 8.56cm !important;
                max-width: 8.56cm !important;
                height: 5.4cm !important;
                max-height: 5.4cm !important;
                opacity: 1 !important;
                filter: none !important;
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }

            .card-header-navy {
                background-color: #0b1f41 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                height: 0.75cm !important;
                min-height: 0.75cm !important;
            }

            .card-body-custom {
                padding: 5px 10px !important;
            }

            .card-footer-soft {
                background-color: #eef4ff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                height: 0.65cm !important;
                min-height: 0.65cm !important;
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
                    <i class="fas fa-solid fa-filter mr-1"></i>Filter Data Kartu Karyawan
                </h6>

                <!-- BADGE TOTAL KARTU TERPILIH & TOMBOL CETAK/EKSPOR -->
                <div class="d-flex align-items-center">
                    <div class="badge badge-primary px-3 py-2 mr-2 shadow-sm d-flex align-items-center"
                        style="font-size: 0.85rem; font-weight: 600;">
                        <i class="fa-solid fa-id-card mr-2"></i>
                        <span>Total: <strong id="selectedCount">0</strong> Kartu</span>
                    </div>

                    <!-- Dropdown Opsi Cetak & Download -->
                    <div class="dropdown">
                        <button class="btn btn-primary btn-sm font-weight-bold shadow-sm dropdown-toggle" type="button"
                            id="dropdownMenuCetak" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-print mr-1"></i> Cetak Kartu
                        </button>
                        <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownMenuCetak">
                            <a class="dropdown-item font-weight-bold text-dark" href="#" onclick="exportCards('pdf')">
                                <i class="fas fa-file-pdf text-danger mr-2"></i> Cetak PDF
                            </a>
                            <a class="dropdown-item font-weight-bold text-dark" href="#" onclick="exportCards('jpg')">
                                <i class="fas fa-file-image text-success mr-2"></i> Cetak JPG
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="row align-items-end">

                    <div class="col-lg-2 col-md-4 mb-3 tanggal-filter-wrap">
                        <label class="font-weight-bold text-dark small mb-1">Filter Tanggal</label>
                        <input type="date" id="tanggalFilter" class="form-control font-weight-bold"
                            value="{{ now()->format('Y-m-d') }}" onchange="applyCardFilter()">
                    </div>

                    <div class="col-lg-3 col-md-6 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Cari Name / Employee No</label>
                        <input type="text" id="cardSearchInput" class="form-control font-weight-bold"
                            placeholder="Name / Employee No..." oninput="applyCardFilter()">
                    </div>

                    <div class="col-lg-2 col-md-4 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Departemen</label>
                        <select id="departemenFilter" class="form-control font-weight-bold" onchange="applyCardFilter()">
                            <option value="">-- Semua --</option>
                            @foreach ($karyawans->pluck('departemen')->filter()->unique()->sort() as $dept)
                                <option value="{{ $dept }}">{{ strtoupper($dept) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Shift</label>
                        <select id="shiftFilter" class="form-control font-weight-bold" onchange="applyCardFilter()">
                            <option value="">-- Semua --</option>
                            <option value="Non Shift">Non Shift</option>
                            <option value="Shift 1">Shift 1</option>
                            <option value="Shift 2">Shift 2</option>
                            <option value="Shift 3">Shift 3</option>
                        </select>
                    </div>

                    <div class="col-lg-2 col-md-4 mb-3">
                        <label class="font-weight-bold text-dark small mb-1">Lokasi</label>
                        <select id="lokasiFilter" class="form-control font-weight-bold" onchange="applyCardFilter()">
                            <option value="">-- Semua --</option>
                            <option value="GCPI 1">GCPI 1</option>
                            <option value="GCPI 2">GCPI 2</option>
                            <option value="GCPI 3">GCPI 3</option>
                        </select>
                    </div>

                    <div class="col-lg-1 col-md-8 mb-3 d-flex">
                        <button type="button" class="btn btn-outline-success font-weight-bold flex-grow-1 mr-2 shadow-sm"
                            onclick="toggleAllVisible(true)" title="Pilih Semua Kartu (Semua Halaman)">
                            <i class="fa-solid fa-circle-check fa-lg"></i>
                        </button>
                        <button type="button" class="btn btn-outline-danger font-weight-bold flex-grow-1 shadow-sm"
                            onclick="toggleAllVisible(false)" title="Batal Pilih Semua (Semua Halaman)">
                            <i class="fa-solid fa-circle-xmark fa-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row" id="cardsGrid">
            @forelse($karyawans as $item)
                @php
                    $empNo = $item->employee_no ?? $item->nif;
                    $empName = $item->name ?? $item->nama;
                    $empDept = $item->departemen ?? $item->dept;
                    $empLoc = $item->location ?? $item->lokasi;
                    $empSchedule = $item->schedule ?? $item->shift;

                    // Tanggal kartu ini berlaku. Sesuaikan nama kolom ($item->tanggal / tanggal_pengambilan)
                    // dengan kolom tanggal yang sebenarnya ada di tabel karyawan/booking Anda.
                    $empTanggalRaw = $item->tanggal
                        ?? $item->tanggal_pengambilan
                        ?? ($item->created_at ?? null);
                    $empTanggal = $empTanggalRaw
                        ? \Illuminate\Support\Carbon::parse($empTanggalRaw)->format('Y-m-d')
                        : now()->format('Y-m-d');
                @endphp
                <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12 card-wrapper" data-nama="{{ strtolower($empName ?? '') }}"
                    data-employee_no="{{ strtolower($empNo ?? '') }}" data-departemen="{{ strtolower($empDept ?? '') }}"
                    data-lokasi="{{ strtolower($empLoc ?? '') }}" data-shift="{{ strtolower($empSchedule ?? '') }}"
                    data-tanggal="{{ $empTanggal }}">

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
                                    style="max-height: 16px; width: auto;">
                            </div>
                        </div>

                        <div class="card-body-custom">
                            <!-- INFORMASI KARYAWAN -->
                            <div class="card-info-area">
                                <div class="info-row">
                                    <span class="info-label">Name</span>
                                    <span class="info-value text-uppercase">: {{ $empName }}</span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">Employee No</span>
                                    <span class="info-value">: {{ $empNo }}</span>
                                </div>

                                <div class="info-row">
                                    <span class="info-label">Departemen</span>
                                    <span class="info-value text-uppercase">: {{ $empDept ?? '-' }}</span>
                                </div>

                                <!-- INSTRUKSI SCAN -->
                                <div class="scan-instruction">
                                    <div class="icon-circle-navy">
                                        <i class="fas fa-qrcode"></i>
                                    </div>
                                    <div class="instruction-text">
                                        Scan barcode ini untuk pengambilan catering
                                    </div>
                                </div>
                            </div>

                            <!-- QR CODE -->
                            <div class="card-qr-area">
                                <div class="qr-container">
                                    <div class="qr-corners-alt"></div>
                                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode($empNo) }}"
                                        alt="QR {{ $empNo }}" class="img-fluid">
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
                                    style="max-height: 12px; width: auto;">
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

        <!-- PAGINATION TAMPILAN (hanya untuk layar, tidak memengaruhi hasil cetak PDF/JPG) -->
        <div class="no-print pagination-bar" id="paginationBar">
            <div class="pagination-info" id="paginationInfo"></div>
            <div class="pagination-controls">
                <button type="button" class="btn btn-sm btn-outline-secondary" id="prevPageBtn"
                    onclick="goToPage(currentPage - 1)">
                    <i class="fas fa-chevron-left"></i> Sebelumnya
                </button>
                <div id="pageNumbers"></div>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="nextPageBtn"
                    onclick="goToPage(currentPage + 1)">
                    Selanjutnya <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Library untuk Ekspor PDF & JPG -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <script>
        var CARDS_PER_PAGE = 10;
        var currentPage = 1;

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
            // "Pilih Semua" berlaku untuk seluruh kartu yang cocok filter di SEMUA halaman,
            // bukan hanya kartu yang sedang tampil di halaman aktif.
            document.querySelectorAll('.card-wrapper:not(.card-hidden-filter)').forEach(function(wrapper) {
                var checkbox = wrapper.querySelector('.card-select-checkbox');
                if (checkbox) {
                    checkbox.checked = state;
                    wrapper.classList.toggle('card-unchecked', !state);
                }
            });
            updateSelectedCounter();
        }

        function clearTanggalFilter() {
            var tanggalInput = document.getElementById('tanggalFilter');
            if (tanggalInput) tanggalInput.value = '';
            applyCardFilter();
        }

        function applyCardFilter() {
            var keyword = document.getElementById('cardSearchInput').value.trim().toLowerCase();
            var dept = document.getElementById('departemenFilter').value.trim().toLowerCase();
            var lokasi = document.getElementById('lokasiFilter').value.trim().toLowerCase();
            var shift = document.getElementById('shiftFilter').value.trim().toLowerCase();
            var tanggal = document.getElementById('tanggalFilter').value; // format YYYY-MM-DD, atau '' = semua tanggal

            document.querySelectorAll('.card-wrapper').forEach(function(wrapper) {
                var nama = wrapper.dataset.nama || '';
                var employeeNo = wrapper.dataset.employee_no || '';
                var departemen = wrapper.dataset.departemen || '';
                var cardLokasi = wrapper.dataset.lokasi || '';
                var cardShift = wrapper.dataset.shift || '';
                var cardTanggal = wrapper.dataset.tanggal || '';

                var matchKeyword = !keyword || nama.includes(keyword) || employeeNo.includes(keyword);
                var matchDept = !dept || departemen === dept;
                var matchLokasi = !lokasi || cardLokasi === lokasi;
                var matchShift = !shift || cardShift === shift;
                var matchTanggal = !tanggal || cardTanggal === tanggal;

                wrapper.classList.toggle('card-hidden-filter', !(matchKeyword && matchDept && matchLokasi &&
                    matchShift && matchTanggal));
            });

            updateSelectedCounter();

            // Setiap kali filter berubah, kembali ke halaman 1
            currentPage = 1;
            renderPagination();
        }

        /* ==================== PAGINATION TAMPILAN (10 kartu / halaman) ==================== */

        function getFilteredWrappers() {
            return Array.prototype.slice.call(
                document.querySelectorAll('.card-wrapper:not(.card-hidden-filter)')
            );
        }

        function renderPagination() {
            var filtered = getFilteredWrappers();
            var totalCards = filtered.length;
            var totalPages = Math.max(1, Math.ceil(totalCards / CARDS_PER_PAGE));

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            filtered.forEach(function(wrapper, idx) {
                var pageOfCard = Math.floor(idx / CARDS_PER_PAGE) + 1;
                wrapper.classList.toggle('card-hidden-page', pageOfCard !== currentPage);
            });

            renderPageNumbers(totalPages);
            renderPaginationInfo(totalCards, totalPages);

            var paginationBar = document.getElementById('paginationBar');
            if (paginationBar) {
                paginationBar.style.display = totalCards > 0 ? 'flex' : 'none';
            }
        }

        function renderPaginationInfo(totalCards, totalPages) {
            var infoEl = document.getElementById('paginationInfo');
            if (!infoEl) return;

            if (totalCards === 0) {
                infoEl.textContent = 'Tidak ada kartu yang cocok dengan filter saat ini.';
                return;
            }

            var start = (currentPage - 1) * CARDS_PER_PAGE + 1;
            var end = Math.min(currentPage * CARDS_PER_PAGE, totalCards);
            infoEl.textContent = 'Menampilkan kartu ' + start + '\u2013' + end + ' dari ' + totalCards +
                ' \u00b7 Halaman ' + currentPage + ' dari ' + totalPages;
        }

        // Render nomor halaman dengan pemendekan (ellipsis) agar rapi walau jumlah halaman banyak
        function renderPageNumbers(totalPages) {
            var container = document.getElementById('pageNumbers');
            if (!container) return;

            container.innerHTML = '';

            var pagesToShow = [];
            var maxAround = 1; // jumlah halaman di kiri/kanan halaman aktif yang ditampilkan

            if (totalPages <= 7) {
                for (var i = 1; i <= totalPages; i++) pagesToShow.push(i);
            } else {
                pagesToShow.push(1);

                var start = Math.max(2, currentPage - maxAround);
                var end = Math.min(totalPages - 1, currentPage + maxAround);

                if (start > 2) pagesToShow.push('...');
                for (var j = start; j <= end; j++) pagesToShow.push(j);
                if (end < totalPages - 1) pagesToShow.push('...');

                pagesToShow.push(totalPages);
            }

            pagesToShow.forEach(function(p) {
                if (p === '...') {
                    var span = document.createElement('span');
                    span.textContent = '...';
                    span.className = 'page-ellipsis';
                    container.appendChild(span);
                    return;
                }

                var btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = p;
                btn.className = 'btn btn-sm page-btn ' + (p === currentPage ? 'btn-primary' : 'btn-outline-primary');
                btn.setAttribute('onclick', 'goToPage(' + p + ')');
                container.appendChild(btn);
            });

            var prevBtn = document.getElementById('prevPageBtn');
            var nextBtn = document.getElementById('nextPageBtn');
            if (prevBtn) prevBtn.disabled = currentPage <= 1;
            if (nextBtn) nextBtn.disabled = currentPage >= totalPages;
        }

        function goToPage(page) {
            currentPage = page;
            renderPagination();

            var printArea = document.getElementById('printArea');
            if (printArea) printArea.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        /* ==================== EKSPOR PDF & JPG (TIDAK terpengaruh pagination/filter tanggal tampilan) ==================== */
        /* exportCards tetap mengambil SEMUA kartu yang tercentang & lolos filter aktif di SEMUA halaman, */
        /* dan ukuran kartu tetap 8.56cm x 5.4cm seperti aslinya. */

        function printSelectedCards() {
            var totalSelected = document.querySelectorAll(
                '.card-wrapper:not(.card-hidden-filter) .card-select-checkbox:checked').length;
            if (totalSelected === 0) {
                alert('Pilih minimal satu kartu karyawan sebelum mencetak.');
                return;
            }
            window.print();
        }

        function exportCards(format) {
            var selectedWrappers = document.querySelectorAll(
                '.card-wrapper:not(.card-hidden-filter) .card-select-checkbox:checked');

            if (selectedWrappers.length === 0) {
                alert('Pilih minimal satu kartu karyawan sebelum mengunduh.');
                return;
            }

            if (format === 'jpg') {
                var exportContainer = document.createElement('div');
                exportContainer.style.position = 'fixed';
                exportContainer.style.left = '-99999px';
                exportContainer.style.top = '0';
                exportContainer.style.padding = '0';
                exportContainer.style.margin = '0';
                exportContainer.style.background = 'transparent';
                exportContainer.style.display = 'flex';
                exportContainer.style.flexWrap = 'wrap';
                exportContainer.style.gap = '8mm';
                exportContainer.style.alignItems = 'flex-start';
                exportContainer.style.justifyContent = 'flex-start';

                selectedWrappers.forEach(function(checkbox) {
                    var cardNode = checkbox.closest('.card-wrapper').querySelector('.catering-card');
                    var cardClone = cardNode.cloneNode(true);
                    cardClone.style.margin = '0';
                    cardClone.style.boxShadow = 'none';
                    cardClone.style.width = '8.56cm';
                    cardClone.style.height = '5.4cm';
                    cardClone.style.maxWidth = '8.56cm';
                    cardClone.style.maxHeight = '5.4cm';
                    cardClone.style.flex = '0 0 8.56cm';
                    cardClone.style.background = '#ffffff';
                    exportContainer.appendChild(cardClone);
                });

                document.body.appendChild(exportContainer);

                html2canvas(exportContainer, {
                    scale: 2,
                    useCORS: true,
                    backgroundColor: null,
                    scrollX: 0,
                    scrollY: 0,
                    logging: false
                }).then(function(canvas) {
                    var link = document.createElement('a');
                    link.download = 'Kartu_Catering_Karyawan.jpg';
                    link.href = canvas.toDataURL('image/jpeg', 0.95);
                    link.click();
                    document.body.removeChild(exportContainer);
                }).catch(function() {
                    document.body.removeChild(exportContainer);
                    alert('Gagal membuat gambar JPG. Silakan coba lagi.');
                });
                return;
            }

            if (format === 'pdf') {
                var pdf = new window.jspdf.jsPDF({
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                });

                var pageWidth = pdf.internal.pageSize.getWidth();
                var pageHeight = pdf.internal.pageSize.getHeight();
                var cardWidth = 85.6;
                var cardHeight = 54;
                var marginX = 12;
                var marginY = 10;
                var gapX = 8;
                var gapY = 8;
                var cols = 2;
                var cardsPerPage = 8;

                var renderPromises = [];

                selectedWrappers.forEach(function(checkbox) {
                    var cardElement = checkbox.closest('.card-wrapper').querySelector('.catering-card');
                    renderPromises.push(
                        html2canvas(cardElement, {
                            scale: 2,
                            useCORS: true,
                            backgroundColor: '#ffffff',
                            scrollX: 0,
                            scrollY: 0,
                            logging: false
                        })
                    );
                });

                Promise.all(renderPromises).then(function(canvases) {
                    canvases.forEach(function(canvas, index) {
                        var pageIndex = Math.floor(index / cardsPerPage);
                        if (index % cardsPerPage === 0 && index !== 0) {
                            pdf.addPage();
                        }

                        var localIndex = index % cardsPerPage;
                        var row = Math.floor(localIndex / cols);
                        var col = localIndex % cols;

                        var x = marginX + col * (cardWidth + gapX);
                        var y = marginY + row * (cardHeight + gapY);

                        if (pageIndex > 0) {
                            var currentPageNum = pageIndex + 1;
                            pdf.setPage(currentPageNum);
                            var pageX = marginX + col * (cardWidth + gapX);
                            var pageY = marginY + row * (cardHeight + gapY);
                            x = pageX;
                            y = pageY;
                        }

                        var imgData = canvas.toDataURL('image/jpeg', 0.98);
                        pdf.addImage(imgData, 'JPEG', x, y, cardWidth, cardHeight, undefined, 'FAST');
                    });

                    pdf.save('Kartu_Catering_Karyawan.pdf');
                }).catch(function() {
                    alert('Gagal membuat file PDF. Silakan coba lagi.');
                });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Panggil applyCardFilter (bukan hanya renderPagination) supaya filter tanggal
            // (default: hari ini) langsung aktif saat halaman pertama kali dibuka.
            applyCardFilter();
        });
    </script>

@endsection