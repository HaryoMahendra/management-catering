@extends('master')

@section('title', 'WhatsApp Catering')

@section('content')
    <!-- Header Judul Halaman -->
    <div class="mb-4">
        <h1 class="h3 mb-0 text-gray-800 font-weight-bold">WhatsApp Catering</h1>
    </div>

    <!-- Card Daftar Karyawan -->
    <div class="card shadow mb-4 border-0">
        <div class="card-header py-3 bg-light border-bottom d-flex align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fab fa-whatsapp mr-2"></i>Daftar Karyawan Penerima QR
            </h6>

            <div>
                <!-- Tombol Kirim Terpilih (Batch) -->
                <button type="button" class="btn btn-success btn-sm shadow-sm font-weight-bold mr-2" onclick="kirimPesanBatch()">
                    <i class="fab fa-whatsapp mr-1"></i> Kirim Ke Terpilih (<span id="countSelected">0</span>)
                </button>

                <!-- Tombol Pengaturan Template Pesan -->
                <button type="button" class="btn btn-primary btn-sm shadow-sm font-weight-bold" data-toggle="modal" data-target="#modalTemplateWA">
                    <i class="fas fa-cog mr-1"></i> Setting Template Pesan
                </button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle" id="dataTableWA" width="100%" cellspacing="0">
                    <thead class="bg-light text-dark font-weight-bold">
                        <tr>
                            <th width="5%" class="text-center">
                                <input type="checkbox" id="checkAll">
                            </th>
                            <th width="5%">No</th>
                            <th>NIF</th>
                            <th>Nama Karyawan</th>
                            <th>Departemen</th>
                            <th>No. WhatsApp</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($karyawans as $index => $item)
                            @php
                                // Normalisasi Nomor HP ke format 62
                                $noHp = preg_replace('/[^0-9]/', '', $item->no_hp);
                                if (substr($noHp, 0, 1) === '0') {
                                    $noHp = '62' . substr($noHp, 1);
                                }

                                // Link ke Catering Card
                                $linkCard = route('kartu.index', ['keyword' => $item->nif]);
                            @endphp
                            <tr>
                                <td class="text-center">
                                    @if ($item->no_hp)
                                        <input type="checkbox" class="check-item" 
                                            data-nohp="{{ $noHp }}"
                                            data-nama="{{ addslashes($item->nama) }}"
                                            data-nif="{{ $item->nif }}"
                                            data-departemen="{{ addslashes($item->departemen ?? '-') }}"
                                            data-link="{{ $linkCard }}"
                                            onchange="updateCounter()">
                                    @else
                                        <input type="checkbox" disabled title="Tidak ada nomor HP">
                                    @endif
                                </td>
                                <td>{{ $index + 1 }}</td>
                                <td class="font-weight-bold text-dark">{{ $item->nif }}</td>
                                <td class="text-uppercase">{{ $item->nama }}</td>
                                <td>{{ $item->departemen ?? '-' }}</td>
                                <td>{{ $item->no_hp ?? '-' }}</td>
                                <td class="text-center">
                                    @if ($item->no_hp)
                                        <!-- Tombol Kirim Satuan -->
                                        <button type="button" 
                                            class="btn btn-outline-success btn-sm font-weight-bold rounded px-2"
                                            onclick="kirimPesanWA('{{ $noHp }}', '{{ addslashes($item->nama) }}', '{{ $item->nif }}', '{{ addslashes($item->departemen ?? '-') }}', '{{ $linkCard }}')">
                                            <i class="fab fa-whatsapp mr-1"></i> Kirim
                                        </button>
                                    @else
                                        <span class="badge badge-secondary p-1">No WA Kosong</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    Belum ada data karyawan dengan Nomor WhatsApp terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL POP-UP PENGATURAN TEMPLATE PESAN -->
    <div class="modal fade" id="modalTemplateWA" tabindex="-1" role="dialog" aria-labelledby="modalTemplateWALabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title font-weight-bold" id="modalTemplateWALabel">
                        <i class="fas fa-edit mr-2"></i> Edit Template Pesan WhatsApp
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="font-weight-bold text-dark">Format Template Pesan:</label>
                        <textarea id="inputTemplateWA" class="form-control font-weight-bold" rows="8"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary font-weight-bold" onclick="resetTemplateDefault()">
                        Reset
                    </button>
                    <button type="button" class="btn btn-success font-weight-bold" onclick="simpanTemplate()">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- DataTables JavaScript & CSS Plugin -->
    <link href="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">
    <script src="{{ asset('sbadmin') }}/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="{{ asset('sbadmin') }}/vendor/datatables/dataTables.bootstrap4.min.js"></script>

    <script>
        // Template Bawaan (Default)
        const defaultTemplate = 
`Halo Yth. *{nama}*,

Berikut kami kirimkan Kartu Catering Digital Anda untuk pengambilan porsi catering.

📋 *Detail Karyawan:*
• NIF: *{nif}*
• Departemen: *{departemen}*

👉 *Klik link di bawah ini untuk membuka Catering Card Anda:*
{link}

Tunjukkan QR Code pada kartu tersebut kepada petugas catering saat melakukan pengambilan.

Terima kasih,
_Management Catering_`;

        // 1. Ambil Template dari LocalStorage Browser
        function getSavedTemplate() {
            return localStorage.getItem('wa_template_catering') || defaultTemplate;
        }

        // 2. Fungsi Formatter URL Pesan WA
        function generateWaUrl(noHp, nama, nif, departemen, link) {
            let template = getSavedTemplate();
            let pesan = template
                .replace(/{nama}/g, nama)
                .replace(/{nif}/g, nif)
                .replace(/{departemen}/g, departemen)
                .replace(/{link}/g, link);

            return `https://api.whatsapp.com/send?phone=${noHp}&text=${encodeURIComponent(pesan)}`;
        }

        // 3. Kirim Pesan Satuan
        function kirimPesanWA(noHp, nama, nif, departemen, link) {
            let url = generateWaUrl(noHp, nama, nif, departemen, link);
            window.open(url, '_blank');
        }

        // 4. Kirim Pesan Sekaligus (Batch / Multi-Tab)
        function kirimPesanBatch() {
            let selectedItems = $('.check-item:checked');

            if (selectedItems.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Pilih Karyawan',
                    text: 'Centang minimal satu karyawan yang ingin dikirimi pesan!',
                    confirmButtonColor: '#4e73df'
                });
                return;
            }

            // Peringatan keamanan browser jika terlalu banyak tab
            if (selectedItems.length > 5) {
                if (!confirm(`Anda memilih ${selectedItems.length} kontak. Rekomendasi aman adalah 5 kontak per sesi agar browser tidak memblokir pop-up. Lanjutkan?`)) {
                    return;
                }
            }

            // Buka Tab Baru Bertahap (Jeda 600ms per Tab)
            selectedItems.each(function (index, el) {
                let noHp = $(el).data('nohp');
                let nama = $(el).data('nama');
                let nif = $(el).data('nif');
                let departemen = $(el).data('departemen');
                let link = $(el).data('link');

                let url = generateWaUrl(noHp, nama, nif, departemen, link);

                setTimeout(function () {
                    window.open(url, '_blank');
                }, index * 600); // Delay 0.6 detik antar tab
            });
        }

        // 5. Update Hitungan Checkbox Terpilih
        function updateCounter() {
            let total = $('.check-item:checked').length;
            $('#countSelected').text(total);
        }

        // 6. Simpan Template
        function simpanTemplate() {
            let teksBaru = $('#inputTemplateWA').val().trim();
            if(!teksBaru) {
                alert('Template tidak boleh kosong!');
                return;
            }
            localStorage.setItem('wa_template_catering', teksBaru);
            $('#modalTemplateWA').modal('hide');

            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: 'Template pesan WhatsApp berhasil disimpan.',
                confirmButtonColor: '#1cc88a'
            });
        }

        // 7. Reset Template
        function resetTemplateDefault() {
            if(confirm('Apakah Anda yakin ingin mengembalikan template ke format awal?')) {
                localStorage.removeItem('wa_template_catering');
                $('#inputTemplateWA').val(defaultTemplate);
            }
        }

        $(document).ready(function() {
            // Inisialisasi DataTables
            $('#dataTableWA').DataTable({
                "language": {
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "zeroRecords": "Data tidak ditemukan",
                    "info": "Menampilkan halaman _PAGE_ dari _PAGES_ (Total _TOTAL_ data)",
                    "infoEmpty": "Tidak ada data tersedia",
                    "search": "Cari Karyawan / NIF / WA:",
                    "paginate": {
                        "previous": "Sebelumnya",
                        "next": "Selanjutnya"
                    }
                },
                "pageLength": 10
            });

            // Handle Check All
            $('#checkAll').click(function() {
                $('.check-item:not(:disabled)').prop('checked', this.checked);
                updateCounter();
            });

            // Isi nilai textarea saat modal dibuka
            $('#modalTemplateWA').on('show.bs.modal', function () {
                $('#inputTemplateWA').val(getSavedTemplate());
            });
        });
    </script>
@endpush