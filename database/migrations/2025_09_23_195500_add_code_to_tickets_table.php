<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Add column (nullable) if missing
        if (!Schema::hasColumn('tickets', 'code')) {
            Schema::table('tickets', function (Blueprint $t) {
                $t->string('code')->nullable()->after('id');
            });
        }

        // 2) Backfill codes for existing rows that are null/empty or duplicated
        $this->backfillUniqueCodes();

        // 3) Add unique index if it does not already exist
        try {
            Schema::table('tickets', function (Blueprint $t) {
                $t->unique('code');
            });
        } catch (\Throwable $e) {
            // index may already exist; ignore
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('tickets', 'code')) {
            Schema::table('tickets', function (Blueprint $t) {
                // drop unique index then column if they exist
                try { $t->dropUnique(['code']); } catch (\Throwable $e) { /* ignore */ }
                $t->dropColumn('code');
            });
        }
    }

    private function backfillUniqueCodes(): void
    {
        // Build a set of existing codes to avoid duplicates
        $existing = [];
        DB::table('tickets')->select('code')->whereNotNull('code')->where('code','!=','')->orderBy('code')->chunk(1000, function ($rows) use (&$existing) {
            foreach ($rows as $row) { $existing[$row->code] = true; }
        });

        $make = function (int $id) use (&$existing): string {
            do {
                $code = strtoupper(bin2hex(random_bytes(4))) . '-' . strtoupper(dechex($id)) . '-' . strtoupper(dechex((int) (microtime(true) * 1000)));
            } while (isset($existing[$code]));
            $existing[$code] = true;
            return $code;
        };

        DB::table('tickets')
            ->select('id','code')
            ->orderBy('id')
            ->chunkById(500, function ($rows) use ($make) {
                foreach ($rows as $row) {
                    $code = (string) ($row->code ?? '');
                    if ($code === '') {
                        DB::table('tickets')->where('id', $row->id)->update(['code' => $make((int) $row->id)]);
                    }
                }
            });
    }
};
