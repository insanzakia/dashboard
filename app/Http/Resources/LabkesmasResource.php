<?php

namespace App\Http\Resources;

use App\Models\Labkesmas;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Labkesmas
 */
class LabkesmasResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_kantor' => $this->nama_kantor,
            'tier_labkesmas' => $this->tier_labkesmas,
            'kabupaten_kota_id' => $this->kabupaten_kota_id,
        ];
    }
}
