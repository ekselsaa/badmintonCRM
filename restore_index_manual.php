<?php
$indexFile = __DIR__ . '/resources/views/pelanggan/membership/index.blade.php';
$content = file_get_contents($indexFile);

// Normalize newlines
$content = str_replace("\r\n", "\n", $content);

// 1. Replace the schedule selection block
$searchBlock = '                            {{-- Tentukan Jadwal Rutin Mingguan --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block mb-3">2. Tentukan Jadwal Rutin Mingguan Anda <span class="text-danger">*</span></label>
                                <div class="row g-3">
                                    {{-- Lapangan --}}
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-secondary">Pilih Lapangan</label>
                                        <select name="lapangan_id" id="select-lapangan" class="form-select" required onchange="checkFormStatus()">
                                            <option value="">-- Pilih Lapangan --</option>
                                            @foreach($lapangans as $lap)
                                                <option value="{{ $lap->id }}" {{ old(\'lapangan_id\') == $lap->id ? \'selected\' : \'\' }}>{{ $lap->nama_lapangan }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    {{-- Hari --}}
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-secondary">Pilih Hari</label>
                                        <select name="hari" id="select-hari" class="form-select" required onchange="onHariChange()">
                                            <option value="">-- Pilih Hari --</option>
                                            <option value="senin">Senin</option>
                                            <option value="selasa">Selasa</option>
                                            <option value="rabu">Rabu</option>
                                            <option value="kamis">Kamis</option>
                                            <option value="jumat">Jumat</option>
                                            <option value="sabtu">Sabtu</option>
                                            <option value="minggu">Minggu</option>
                                        </select>
                                    </div>
                                    {{-- Sesi --}}
                                    <div class="col-md-4">
                                        <label class="form-label small fw-bold text-secondary">Pilih Sesi (Shift 3 Jam)</label>
                                        <select name="sesi" id="select-sesi" class="form-select" required onchange="checkFormStatus()" disabled>
                                            <option value="">-- Pilih Sesi --</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-text text-muted small mt-2">
                                    * Ketersediaan hari dan sesi menyesuaikan dengan kategori paket member yang dipilih.
                                </div>
                            </div>

                            {{-- Pilihan Metode Pembayaran Teroptimasi (Hanya QRIS & Tunai) --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block mb-3">3. Pilih Metode Pembayaran <span class="text-danger">*</span></label>';

$replaceBlock = '                            {{-- Pilihan Metode Pembayaran Teroptimasi (Hanya QRIS & Tunai) --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold d-block mb-3">2. Pilih Metode Pembayaran <span class="text-danger">*</span></label>';

$searchBlockNorm = str_replace("\r\n", "\n", $searchBlock);
$replaceBlockNorm = str_replace("\r\n", "\n", $replaceBlock);

if (strpos($content, $searchBlockNorm) !== false) {
    $content = str_replace($searchBlockNorm, $replaceBlockNorm, $content);
    echo "Replaced schedule selection inputs block.\n";
} else {
    echo "WARNING: Could not find schedule selection inputs block.\n";
}

// 2. Replace the script block
$scriptStartPos = strpos($content, '<script>');
$scriptEndPos = strpos($content, '</script>', $scriptStartPos);

if ($scriptStartPos !== false && $scriptEndPos !== false) {
    $originalScript = '<script>
    // Memilih Paket Membership
    function selectPackage(element) {
        // Hapus class active dari pilihan paket lain
        document.querySelectorAll(\'.membership-card-option\').forEach(card => {
            card.classList.remove(\'active\');
        });
        
        // Tambahkan class active ke paket yang dipilih
        element.classList.add(\'active\');
        
        // Simpan nilai pilihan di hidden input
        const val = element.getAttribute(\'data-value\');
        document.getElementById(\'input-paket\').value = val;

        checkFormStatus();
    }

    // Memilih Metode Pembayaran
    function selectPaymentMethod(element) {
        // Hapus class active dari pilihan metode lain
        document.querySelectorAll(\'.payment-method-card\').forEach(card => {
            card.classList.remove(\'active\');
        });
        
        // Tambahkan class active ke metode yang dipilih
        element.classList.add(\'active\');
        
        // Simpan nilai pilihan di hidden input
        const val = element.getAttribute(\'data-value\');
        document.getElementById(\'input-metode\').value = val;

        // Tampilkan/sembunyikan wadah instruksi dan file upload
        const qrisSec = document.getElementById(\'instruksi-qris\');
        const tunaiSec = document.getElementById(\'instruksi-tunai\');
        const uploadCont = document.getElementById(\'upload-container\');

        if (val === \'qris\') {
            qrisSec.classList.remove(\'d-none\');
            tunaiSec.classList.add(\'d-none\');
        } else if (val === \'tunai\') {
            tunaiSec.classList.remove(\'d-none\');
            qrisSec.classList.add(\'d-none\');
        }

        // Tampilkan input upload begitu metode pembayaran dipilih
        uploadCont.classList.remove(\'d-none\');

        checkFormStatus();
    }

    // Memicu klik pada file input tersembunyi
    function triggerFileInput() {
        document.getElementById(\'bukti_pembayaran\').click();
    }

    // Menampilkan pratinjau gambar bukti transfer
    function previewImage(input) {
        const file = input.files[0];
        if (file) {
            // Validasi ukuran berkas (2MB = 2 * 1024 * 1024 bytes)
            if (file.size > 2 * 1024 * 1024) {
                alert("Ukuran berkas maksimal 2MB. Berkas yang Anda pilih berukuran " + (file.size / (1024 * 1024)).toFixed(2) + "MB.");
                resetFileSelection();
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(\'image-preview\').src = e.target.result;
                document.getElementById(\'upload-prompt\').classList.add(\'d-none\');
                document.getElementById(\'preview-container\').classList.remove(\'d-none\');
                document.getElementById(\'file-name-label\').innerText = file.name;
                checkFormStatus();
            }
            reader.readAsDataURL(file);
        }
    }

    // Mereset berkas bukti yang terpilih
    function resetFileSelection(event) {
        if (event) {
            event.stopPropagation(); // Cegah memicu klik upload-zone kembali
        }
        document.getElementById(\'bukti_pembayaran\').value = "";
        document.getElementById(\'upload-prompt\').classList.remove(\'d-none\');
        document.getElementById(\'preview-container\').classList.add(\'d-none\');
        document.getElementById(\'image-preview\').src = "#";
        checkFormStatus();
    }

    // Memeriksa kelengkapan form untuk mengaktifkan tombol submit
    function checkFormStatus() {
        const paketSelected = document.getElementById(\'input-paket\').value;
        const metodeSelected = document.getElementById(\'input-metode\').value;
        const fileUploaded = document.getElementById(\'bukti_pembayaran\').files.length > 0;
        const btnSubmit = document.getElementById(\'btn-submit\');

        if (paketSelected && metodeSelected && fileUploaded) {
            btnSubmit.removeAttribute(\'disabled\');
        } else {
            btnSubmit.setAttribute(\'disabled\', \'disabled\');
        }
    }
</script>';

    $content = substr($content, 0, $scriptStartPos) . str_replace("\r\n", "\n", $originalScript) . substr($content, $scriptEndPos + strlen('</script>'));
    echo "Replaced script block.\n";
} else {
    echo "ERROR: Could not find script block.\n";
}

file_put_contents($indexFile, $content);
echo "Saved reverted index.blade.php\n";
