<?php

namespace App\Support;

use Illuminate\Http\Request;

/**
 * DTO immutable untuk parameter filter dashboard.
 * Mengubah query-string mentah menjadi objek terketik → dilempar ke Repository.
 */
final readonly class DashboardFilter
{
    public function __construct(
        public ?string $regionalId = null,
        public ?string $provinsiId = null,
        public ?string $kabupatenKotaId = null,
        public ?int $tier = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $tier = $request->query('tier');

        return new self(
            regionalId: $request->query('regional_id') ?: null,
            provinsiId: $request->query('provinsi_id') ?: null,
            kabupatenKotaId: $request->query('kabupaten_kota_id') ?: null,
            tier: is_numeric($tier) ? (int) $tier : null,
        );
    }
}
