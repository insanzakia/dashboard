<?php

namespace App\Http\Controllers;

use App\Http\Resources\KabupatenKotaResource;
use App\Http\Resources\ProvinsiResource;
use App\Http\Resources\RegionalResource;
use App\Repositories\Contracts\WilayahRepositoryInterface;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Endpoint JSON untuk filter kaskade wilayah. Thin: delegasi ke WilayahRepository,
 * bungkus hasil dengan Resource + envelope ApiResponse.
 */
class WilayahController extends Controller
{
    public function __construct(
        private readonly WilayahRepositoryInterface $wilayah,
    ) {}

    public function regional(): JsonResponse
    {
        try {
            $data = RegionalResource::collection($this->wilayah->listRegional());

            return ApiResponse::success($data);
        } catch (Throwable $e) {
            Log::error('Gagal memuat regional', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat data regional.', 500);
        }
    }

    public function provinsi(Request $request): JsonResponse
    {
        $regionalId = (string) $request->query('regional_id', '');

        if ($regionalId === '') {
            return ApiResponse::success([]);
        }

        try {
            $data = ProvinsiResource::collection($this->wilayah->listProvinsi($regionalId));

            return ApiResponse::success($data);
        } catch (Throwable $e) {
            Log::error('Gagal memuat provinsi', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat data provinsi.', 500);
        }
    }

    public function kabupatenKota(Request $request): JsonResponse
    {
        $provinsiId = (string) $request->query('provinsi_id', '');

        if ($provinsiId === '') {
            return ApiResponse::success([]);
        }

        try {
            $data = KabupatenKotaResource::collection($this->wilayah->listKabupatenKota($provinsiId));

            return ApiResponse::success($data);
        } catch (Throwable $e) {
            Log::error('Gagal memuat kabupaten/kota', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat data kabupaten/kota.', 500);
        }
    }
}
