<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Kartu Catering Karyawan</title>
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <!-- FontAwesome Font for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    

    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .catering-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid #e0e0e0;
            transition: transform 0.2s;
        }

        .catering-card:hover {
            transform: translateY(-3px);
        }


        .card-header-navy {
            background-color: #0b1f41;
            color: #ffffff;
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header-navy h4 {
            margin: 0;
            font-weight: 800;
            letter-spacing: 1.5px;
            font-size: 1.4rem;
        }

        .card-body-custom {
            padding: 20px;
        }

        .info-row {
            border-bottom: 1px solid #e9ecef;
            padding-bottom: 8px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .info-label {
            font-weight: 700;
            color: #0b1f41;
            width: 100px;
            font-size: 0.95rem;
        }

        .info-value {
            font-weight: 800;
            color: #111111;
            font-size: 1.05rem;
        }


        .scan-instruction {
            display: flex;
            align-items: center;
            margin-top: 15px;
        }

        .icon-circle-navy {
            width: 38px;
            height: 38px;
            background-color: #0b1f41;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .instruction-text {
            font-size: 0.8rem;
            font-weight: 700;
            color: #333333;
            line-height: 1.2;
        }


        .qr-container {
            position: relative;
            padding: 10px;
            display: inline-block;
        }

        .qr-container img {
            max-width: 100%;
            height: auto;
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
            padding: 10px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-top: 1px solid #dce6f7;
        }

        .footer-note {
            display: flex;
            align-items: center;
            font-size: 0.78rem;
            font-weight: 700;
            color: #0b1f41;
        }

        .icon-circle-check {
            width: 22px;
            height: 22px;
            background-color: #0b1f41;
            color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            margin-right: 8px;
            flex-shrink: 0;
        }

        .company-logo-text {
            font-weight: 900;
            color: #0b1f41;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
        }


        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm;
            }

            .no-print {
                display: none !important;
            }

            body {
                background-color: #ffffff !important;
                padding: 0 !important;
            }

            .container {
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


            .col-lg-6,
            .col-md-12 {
                flex: 0 0 50% !important;
                max-width: 50% !important;
                padding: 3mm !important;
                box-sizing: border-box !important;
            }


            .catering-card {
                box-shadow: none !important;
                border: 1px solid #111111 !important;
                page-break-inside: avoid !important;
                width: 8.5cm !important;
                height: 5.4cm !important;
                margin: 0 auto !important;
                border-radius: 6px !important;
                display: flex !important;
                flex-direction: column !important;
                justify-content: space-between !important;
            }

            .card-header-navy {
                background-color: #0b1f41 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                padding: 4px 10px !important;
                height: 0.9cm !important;
            }

            .card-header-navy h4 {
                font-size: 0.78rem !important;
                letter-spacing: 0.5px !important;
            }

            .card-body-custom {
                padding: 6px 10px !important;
                flex-grow: 1 !important;
                display: flex !important;
                align-items: center !important;
            }

            .info-row {
                padding-bottom: 2px !important;
                margin-bottom: 3px !important;
            }

            .info-label {
                width: 60px !important;
                font-size: 0.65rem !important;
            }

            .info-value {
                font-size: 0.68rem !important;
            }

            .scan-instruction {
                margin-top: 4px !important;
            }

            .icon-circle-navy {
                width: 22px !important;
                height: 22px !important;
                font-size: 0.6rem !important;
                margin-right: 6px !important;
            }

            .instruction-text {
                font-size: 0.52rem !important;
            }

            .qr-container {
                padding: 3px !important;
            }

            .qr-container img {
                width: 2.1cm !important;
                height: 2.1cm !important;
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
                padding: 4px 10px !important;
                height: 0.8cm !important;
            }

            .footer-note {
                font-size: 0.5rem !important;
            }

            .icon-circle-check {
                width: 12px !important;
                height: 12px !important;
                font-size: 0.45rem !important;
                margin-right: 4px !important;
            }

            .company-logo-text {
                font-size: 0.55rem !important;
            }
        }
    </style>
</head>

<body>

    <div class="container my-4">
        <!-- Action Bar / Header Halaman (Tidak Ikut Tercetak) -->
        <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom no-print">
            <div>
                <h3 class="font-weight-bold text-dark mb-1">Cetak Kartu Catering Karyawan</h3>
                <p class="text-muted mb-0">Tampilan siap cetak dengan format ID Card standar catering.</p>
            </div>
            <div>
                <a href="{{ route('booking.index') }}" class="btn btn-secondary font-weight-bold mr-2">
                    &leftarrow; Kembali
                </a>
                <button onclick="window.print()" class="btn btn-primary font-weight-bold shadow-sm">
                    <i class="fas fa-print mr-1"></i> Cetak 
                </button>
            </div>
        </div>

        <!-- Grid Daftar Kartu Karyawan -->
        <div class="row">
            @forelse($karyawans as $item)
                <div class="col-lg-6 col-md-12 mb-4">
                    <div class="catering-card">

                        <!-- 1. Header Kartu -->
                        <div class="card-header-navy">
                            <h4>CATERING CARD</h4>
                            <i class="fas fa-utensils fa-lg"></i>
                        </div>

                        <!-- 2. Body Kartu (Kiri: Teks & Kanan: QR Code) -->
                        <div class="card-body-custom">
                            <div class="row align-items-center no-gutters w-100">

                                <!-- Kolom Kiri: Detail Informasi -->
                                <div class="col-7 pr-1">
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

                                <!-- Kolom Kanan: QR Code dengan Siku Biru Gelap -->
                                <div class="col-5 text-center pl-1">
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

</body>

</html>
