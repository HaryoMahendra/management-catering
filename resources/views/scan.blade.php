@extends('master')

@section('title', 'Scan')

@section('content')
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">
            Scan QR Catering
        </h1>
        <p class="text-muted mb-0">Scan barcode karyawan atau masukkan NIF secara manual</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header py-3 bg-primary text-white d-flex align-items-center">
                    <i class="fas fa-camera mr-2"></i>
                    <h6 class="m-0 font-weight-bold">Scan Barcode atau Masukkan NIF</h6>
                </div>

                <div class="card-body p-4">
                    <!-- Area Scanner -->
                    <div class="scanner-wrapper rounded overflow-hidden border mb-4">
                        <div id="reader" style="width: 100%;"></div>
                    </div>

                    <!-- Input Manual NIF -->
                    <div class="manual-input-section">
                        <label class="small text-gray-600 font-weight-bold text-uppercase mb-2">
                            <i class="fas fa-keyboard mr-1"></i> Masukkan NIF Manual
                        </label>
                        <div class="input-group input-group-lg">
                            <input
                                type="text"
                                id="manual_nif"
                                class="form-control"
                                placeholder="Contoh: NIF-001"
                                onkeydown="if(event.key === 'Enter'){ submitManualNif(); }"
                            >
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
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function processNifScan(nifCode) {
        let resultDiv = document.getElementById('scan-result');
        resultDiv.innerHTML = `
            <div class="alert alert-info d-flex align-items-center">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                <span>Memproses NIF: <b>${nifCode}</b>...</span>
            </div>`;

        fetch("{{ route('scan.process') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ nif: nifCode })
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
                resultDiv.innerHTML = `
                    <div class="alert alert-success text-center py-4">
                        <i class="fas fa-check-circle fa-3x d-block mb-3 text-success"></i>
                        <h5 class="font-weight-bold mb-2">BERHASIL!</h5>
                        <p class="mb-3">${data.message}</p>
                        <hr class="my-3">
                        <div class="text-left">
                            <div class="mb-1"><b>Nama:</b> ${data.karyawan.nama}</div>
                            <div><b>Dept:</b> ${data.karyawan.departemen} (${data.karyawan.lokasi})</div>
                        </div>
                    </div>`;
            } else {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger text-center py-4">
                        <i class="fas fa-times-circle fa-3x d-block mb-3 text-danger"></i>
                        <h5 class="font-weight-bold mb-2">GAGAL!</h5>
                        <p class="mb-0">${data.message}</p>
                    </div>`;
            }
        })
        .catch(err => {
            console.error("Detail Error:", err);
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <b>Error System:</b> ${err.message}
                </div>`;
        });
    }

    function submitManualNif() {
        let nifInput = document.getElementById('manual_nif').value.trim();
        if (nifInput) {
            processNifScan(nifInput);
            document.getElementById('manual_nif').value = '';
        }
    }

    let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render((decodedText) => processNifScan(decodedText));

    // Auto-isi & proses otomatis kalau NIF dikirim lewat parameter URL (?nif=NIF-001)
    document.addEventListener('DOMContentLoaded', function () {
        const params = new URLSearchParams(window.location.search);
        const nifFromUrl = params.get('nif');
        if (nifFromUrl) {
            document.getElementById('manual_nif').value = nifFromUrl;
            processNifScan(nifFromUrl);
        }
    });
</script>
@endpush