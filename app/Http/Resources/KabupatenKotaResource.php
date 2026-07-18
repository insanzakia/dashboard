<?php

namespace App\Http\Resources;

use App\Models\KabupatenKota;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin KabupatenKota
 */
class KabupatenKotaResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'provinsi_id' => $this->provinsi_id,
        ];
    }
}
