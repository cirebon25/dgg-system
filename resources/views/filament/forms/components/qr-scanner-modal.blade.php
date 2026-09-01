<div x-data="qrScanner()" class="flex flex-col items-center justify-center space-y-4 p-4">
    <div id="reader" style="width: 100%; max-width: 400px;" class="rounded-xl overflow-hidden shadow-lg"></div>
    <div id="result-message" class="text-sm font-medium text-success-600"></div>
    <p class="text-xs text-gray-500 text-center">Arahkan kamera ke QR Code pada mesin fotokopi.</p>
</div>

<!-- Load Library HTML5 QR Code via CDN -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    function qrScanner() {
        return {
            init() {
                const html5QrCode = new Html5Qrcode("reader");
                const config = {
                    fps: 10,
                    qrbox: {
                        width: 250,
                        height: 250
                    }
                };

                html5QrCode.start({
                        facingMode: "environment"
                    },
                    config,
                    (decodedText, decodedResult) => {
                        // Cari option pada Select machine_id berdasarkan serial number yang di-scan
                        // decodedText diasumsikan adalah Serial Number (serial_number) mesin
                        let selectEl = document.querySelector(
                            '[wire\\:model\\.defer$="data.machine_id"], [wire\\:model$="data.machine_id"]');

                        if (decodedText) {
                            // Hentikan scanner setelah berhasil
                            html5QrCode.stop().then(() => {
                                document.getElementById('result-message').innerText = "Berhasil scan: " +
                                    decodedText;

                                // Kirim value ke Livewire form state
                                @this.set('data.machine_id', decodedText);

                                // Tutup modal otomatis setelah 1 detik
                                setTimeout(() => {
                                    window.dispatchEvent(new CustomEvent('close-modal', {
                                        detail: {
                                            id: 'modal'
                                        }
                                    }));
                                }, 1000);
                            }).catch(err => console.log(err));
                        }
                    },
                    (errorMessage) => {
                        // Error scan frame (biasanya diabaikan karena sedang mencari QR)
                    }
                ).catch(err => {
                    console.error("Gagal membuka kamera:", err);
                    document.getElementById('result-message').innerText =
                        "Gagal mengakses kamera. Pastikan izin kamera aktif.";
                });
            }
        }
    }
</script>
