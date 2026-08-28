@extends('master')

@section('title', 'Scan')

@section('content')
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            Scan QR Catering
        </h1>
        <p class="text-muted mb-0">Scan barcode karyawan atau masukkan NIK secara manual</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header py-3 bg-primary text-white d-flex align-items-center">
                    <i class="fas fa-camera mr-2"></i>
                    <h6 class="m-0 font-weight-bold">Scan Barcode atau Masukkan NIK</h6>
                </div>

                <div class="card-body p-4">
                    <!-- Area Scanner -->
                    <div class="scanner-wrapper rounded overflow-hidden border mb-4">
                        <div id="reader" style="width: 100%;"></div>
                    </div>

                    <div class="manual-input-section">
                        <label class="small text-gray-600 font-weight-bold text-uppercase mb-2">
                            <i class="fas fa-keyboard mr-1"></i> Masukkan NIK Manual
                        </label>
                        <div class="input-group input-group-lg">
                            <input type="text" id="manual_nif" class="form-control" placeholder="Contoh: NIK-001"
                                onkeydown="if(event.key === 'Enter'){ submitManualNif(); }">
                            <div class="input-group-append">
                                <button class="btn btn-primary font-weight-bold" type="button" onclick="submitManualNif()">
                                    <i class="fas fa-paper-plane mr-1"></i> Submit
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Hasil Scan -->
                    <div id="scan-result" class="mt-4"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .scanner-wrapper {
            background-color: #000;
            min-height: 250px;
        }

        #reader {
            border: none !important;
        }

        #reader video {
            border-radius: 0.25rem;
        }

        .manual-input-section {
            background-color: #f8f9fc;
            padding: 1rem;
            border-radius: 0.5rem;
        }

        #scan-result .alert {
            border-radius: 0.5rem;
        }

        #scan-result .alert h5 {
            letter-spacing: 0.5px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/html5-qrcode"></script>

    <script>
        const AudioContext = window.AudioContext || window.webkitAudioContext;
        const audioCtx = new AudioContext();

        function playSuccessSound() {
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();

            osc.connect(gain);
            gain.connect(audioCtx.destination);

            const now = audioCtx.currentTime;
            osc.type = 'sine';

            osc.frequency.setValueAtTime(600, now);
            osc.frequency.setValueAtTime(900, now + 0.1);

            gain.gain.setValueAtTime(0.3, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);

            osc.start(now);
            osc.stop(now + 0.35);
        }

        function playErrorSound() {
            if (audioCtx.state === 'suspended') {
                audioCtx.resume();
            }

            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();

            osc.connect(gain);
            gain.connect(audioCtx.destination);

            const now = audioCtx.currentTime;
            osc.type = 'sawtooth';

            osc.frequency.setValueAtTime(220, now);
            osc.frequency.setValueAtTime(180, now + 0.15);

            gain.gain.setValueAtTime(0.3, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.4);

            osc.start(now);
            osc.stop(now + 0.4);
        }

        let isProcessing = false;

        function processNifScan(nifCode) {
            if (isProcessing) return;
            isProcessing = true;

            Swal.fire({
                title: 'Memproses...',
                text: `Memeriksa data NIK: ${nifCode}`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch("{{ route('scan.process') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        nif: nifCode
                    })
                })
                .then(async response => {
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        return response.json();
                    } else {
                        const text = await response.text();
                        throw new Error(`Server Error (${response.status}): ${text}`);
                    }
                })
                .then(data => {
                    if (data.success) {
                        playSuccessSound();

                        Swal.fire({
                            icon: 'success',
                            title: '<span style="color:#28a745; font-weight:800;">BERHASIL!</span>',
                            html: `
            <p class="mb-2">${data.message}</p>
            <hr class="my-2">
            <div class="text-left small bg-light p-3 rounded">
                <div><b>Nama :</b> ${data.karyawan.nama}</div>
                <div><b>Dept :</b> ${data.karyawan.departemen ?? '-'} (${data.karyawan.lokasi ?? '-'})</div>
                <div><b>Shift :</b> ${data.karyawan.shift ?? '-'}</div>
            </div>
        `,
                            showConfirmButton: true,
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#28a745',
                            allowOutsideClick: false
                        }).then(() => {
                            isProcessing = false;
                        });
                    } else {
                        playErrorSound();

                        Swal.fire({
                            icon: 'error',
                            title: '<span style="color:#dc3545; font-weight:800;">GAGAL!</span>',
                            text: data.message,
                            confirmButtonText: 'Tutup',
                            confirmButtonColor: '#dc3545',
                            // timer: 3500,
                            // timerProgressBar: true
                        }).then(() => {
                            isProcessing = false;
                        });
                    }
                })
                .catch(err => {
                    playErrorSound();
                    console.error("Detail Error:", err);

                    Swal.fire({
                        icon: 'error',
                        title: 'Error Sistem',
                        text: err.message,
                        confirmButtonColor: '#dc3545'
                    }).then(() => {
                        isProcessing = false;
                    });
                });
        }

        function submitManualNif() {
            let nifInput = document.getElementById('manual_nif').value.trim();
            if (nifInput) {
                processNifScan(nifInput);
                document.getElementById('manual_nif').value = '';
            }
        }

        let html5QrcodeScanner = new Html5QrcodeScanner("reader", {
            fps: 10,
            qrbox: 250
        });
        html5QrcodeScanner.render((decodedText) => processNifScan(decodedText));

        document.addEventListener('DOMContentLoaded', function() {
            const params = new URLSearchParams(window.location.search);
            const nifFromUrl = params.get('nif');
            if (nifFromUrl) {
                document.getElementById('manual_nif').value = nifFromUrl;
                processNifScan(nifFromUrl);
            }
        });
    </script>
@endpush
