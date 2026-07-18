<?php

namespace App\Http\Resources;

use App\Models\Regional;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Regional
 */
class RegionalResource extends JsonResource
{
    /**
     * Bentuk output cocok dengan tipe `Regional` di frontend (resources/js/types/wilayah.ts).
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'negara_id' => $this->negara_id,
        ];
    }
}
