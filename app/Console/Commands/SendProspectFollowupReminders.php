<?php

namespace App\Console\Commands;

use App\Models\ProspectVisit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendProspectFollowupReminders extends Command
{
    protected $signature = 'prospects:send-followup-reminders';

    protected $description = 'Kirim WA pengingat follow-up prospect yang jatuh tempo';

    private const GATEWAY_URL = 'http://localhost:3001/send';
    private const LOOKBACK_DAYS = 7;

    public function handle(): int
    {
        $sent = 0;
        $failed = 0;

        ProspectVisit::query()
            ->with(['prospect', 'marketing'])
            ->where('tanggal_followup', '<=', today())
            ->where('tanggal_followup', '>=', today()->subDays(self::LOOKBACK_DAYS))
            ->whereNull('followup_reminded_at')
            ->chunkById(100, function ($visits) use (&$sent, &$failed) {
                foreach ($visits as $visit) {
                    $nomor = $this->normalizeNomor($visit->marketing?->no_telp);

                    if (! $visit->marketing?->aktif || $nomor === null) {
                        $failed++;
                        Log::warning('Follow-up dilewati: marketing tidak aktif atau nomor tidak valid.', [
                            'visit_id' => $visit->id,
                        ]);

                        continue;
                    }

                    try {
                        $response = Http::timeout(10)->post(self::GATEWAY_URL, [
                            'number' => $nomor,
                            'message' => $this->susunPesan($visit),
                        ]);

                        if (! $response->successful()) {
                            throw new \RuntimeException('Gateway HTTP ' . $response->status());
                        }

                        $visit->forceFill(['followup_reminded_at' => now()])->saveQuietly();
                        $sent++;
                    } catch (Throwable $e) {
                        $failed++;
                        Log::warning('Follow-up WA gagal: ' . $e->getMessage(), [
                            'visit_id' => $visit->id,
                        ]);
                    }
                }
            });

        $this->info("Terkirim: {$sent}, gagal/dilewati: {$failed}");

        return self::SUCCESS;
    }

    private function normalizeNomor(?string $nomor): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $nomor);

        if ($digits === '') {
            return null;
        }

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        }

        return preg_match('/^62\d{8,13}$/', $digits) ? $digits : null;
    }

    private function susunPesan(ProspectVisit $visit): string
    {
        return "*Pengingat Follow-up*\n\n"
            . 'Perusahaan: ' . ($visit->prospect?->nama_perusahaan ?? '-') . "\n"
            . 'Tanggal follow-up: ' . $visit->tanggal_followup->format('d/m/Y') . "\n"
            . 'Catatan: ' . ($visit->catatan ?: '-') . "\n\n"
            . 'Mohon segera ditindaklanjuti. Terima kasih.';
    }
}