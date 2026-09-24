<?php

use App\Models\AccommodationClaim;
use App\Models\CashLedger;
use App\Models\User;
use function Pest\Laravel\{actingAs, assertDatabaseMissing};

beforeEach(function () {
    $this->user = User::factory()->create(['name' => 'RUDI']);
    actingAs($this->user);
});

test('input klaim akomodasi luar kota TIDAK BOLEH memotong kas umum', function () {
    // Simulasi admin menginput klaim akomodasi luar kota sebesar Rp 750.000
    $claim = AccommodationClaim::create([
        'wilayah' => 'BANDUNG',
        'dari_tanggal' => '2026-06-08',
        'sampai_tanggal' => '2026-06-10',
        'biaya_transportasi' => 400000,
        'konsumsi_karyawan' => 350000,
        'pengeluaran_lain_1' => 0,
        'pengeluaran_lain_2' => 0,
        'status' => 'APPROVED'
    ]);

    // Memastikan tidak ada entitas Kas Umum yang terbuat dari ID klaim ini
    assertDatabaseMissing('cash_ledgers', [
        'no_surat' => 'AC-CLAIM-' . $claim->id,
    ]);

    // Memastikan total pengeluaran kas umum tetap tidak terpengaruh oleh form akomodasi
    expect(CashLedger::where('no_surat', 'like', 'AC-CLAIM-%')->count())->toBe(0);
});