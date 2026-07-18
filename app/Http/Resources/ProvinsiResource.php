<?php

namespace App\Http\Resources;

use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Provinsi
 */
class ProvinsiResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama' => $this->nama,
            'regional_id' => $this->regional_id,
        ];
    }
}
