@extends('master')

@section('title', 'Scan')

@section('content')
    <!-- Page Heading -->
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">Scan QR Catering</h1>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold"></i>Scan Barcode atau Masukkan NIF</h6>
                </div>
                <div class="card-body text-center">
                    <div id="reader" style="width: 100%;"></div>

                    <div class="mt-4 pt-3 border-top">
                        <label class="small text-gray-600 font-weight-bold">Masukkan NIF:</label>
                        <div class="input-group">
                            <input type="text" id="manual_nif" class="form-control" placeholder="Contoh: NIF-001">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button" onclick="submitManualNif()">Submit</button>
                            </div>
                        </div>
                    </div>

                    <div id="scan-result" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    function processNifScan(nifCode) {
    let resultDiv = document.getElementById('scan-result');
    resultDiv.innerHTML = `<div class="alert alert-info"><i class="fas fa-spinner fa-spin mr-2"></i>Memproses NIF: <b>${nifCode}</b>...</div>`;

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
        if(data.success) {
            resultDiv.innerHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle fa-2x d-block mb-2"></i>
                    <h5><b>BERHASIL!</b></h5>
                    ${data.message}<br>
                    <hr class="my-2">
                    <b>Nama:</b> ${data.karyawan.nama} <br>
                    <b>Dept:</b> ${data.karyawan.departemen} (${data.karyawan.lokasi})
                </div>`;
        } else {
            resultDiv.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-times-circle fa-2x d-block mb-2"></i>
                    <h5><b>GAGAL!</b></h5>
                    ${data.message}
                </div>`;
        }
    })
    .catch(err => {
        console.error("Detail Error:", err);
        resultDiv.innerHTML = `<div class="alert alert-danger"><b>Error System:</b> ${err.message}</div>`;
    });
}

    function submitManualNif() {
        let nifInput = document.getElementById('manual_nif').value;
        if(nifInput) {
            processNifScan(nifInput);
            document.getElementById('manual_nif').value = '';
        }
    }

    let html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: 250 });
    html5QrcodeScanner.render((decodedText) => processNifScan(decodedText));
</script>
@endpush