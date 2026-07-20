<?php

namespace App\Http\Controllers;

use App\Models\Labkesmas;
use App\Repositories\Contracts\InventarisAlatRepositoryInterface;
use App\Repositories\Contracts\PemenuhanAlatRepositoryInterface;
use App\Support\ApiResponse;
use App\Support\DashboardFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Dashboard Standar Peralatan Labkesmas.
 * Halaman Inertia + endpoint JSON (envelope ApiResponse) untuk data pemenuhan alat.
 */
class StandarLabkesmasController extends Controller
{
    public function index(InventarisAlatRepositoryInterface $repo): Response
    {
        return Inertia::render('StandarLabkesmas/Index', [
            'labkesmasOptions' => $repo->labkesmasOptions(),
        ]);
    }

    /** Endpoint JSON: rincian pemenuhan alat satu Labkesmas. */
    public function fulfillment(Labkesmas $labkesmas, PemenuhanAlatRepositoryInterface $repo): JsonResponse
    {
        try {
            return ApiResponse::success($repo->labFulfillment($labkesmas->id));
        } catch (Throwable $e) {
            Log::error('Gagal memuat pemenuhan alat lab', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat data pemenuhan. Silakan coba lagi.', 500);
        }
    }

    /** Endpoint JSON: agregasi pemenuhan sesuai filter wilayah/tier (rata-rata antar-lab). */
    public function aggregate(Request $request, PemenuhanAlatRepositoryInterface $repo): JsonResponse
    {
        try {
            return ApiResponse::success($repo->aggregateFulfillment(DashboardFilter::fromRequest($request)));
        } catch (Throwable $e) {
            Log::error('Gagal memuat agregat pemenuhan alat', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat agregat pemenuhan. Silakan coba lagi.', 500);
        }
    }

    /** Endpoint JSON: perbandingan pemenuhan antar-lab dalam cakupan. */
    public function comparison(Request $request, PemenuhanAlatRepositoryInterface $repo): JsonResponse
    {
        try {
            return ApiResponse::success($repo->labComparison(DashboardFilter::fromRequest($request)));
        } catch (Throwable $e) {
            Log::error('Gagal memuat perbandingan pemenuhan alat', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat perbandingan. Silakan coba lagi.', 500);
        }
    }

    /** Endpoint JSON: agregasi % dikelompokkan menurut dimensi (tier/provinsi/regional/kab-kota). */
    public function grouped(Request $request, PemenuhanAlatRepositoryInterface $repo): JsonResponse
    {
        $allowed = ['tier', 'provinsi', 'regional', 'kabupaten_kota'];
        $groupBy = in_array($request->query('group_by'), $allowed, true) ? $request->query('group_by') : 'tier';

        try {
            return ApiResponse::success($repo->groupedFulfillment(DashboardFilter::fromRequest($request), $groupBy));
        } catch (Throwable $e) {
            Log::error('Gagal memuat agregasi terkelompok', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat data. Silakan coba lagi.', 500);
        }
    }

    /** Endpoint JSON: pemenuhan beberapa lab terpilih (perbandingan berdampingan). */
    public function multi(Request $request, PemenuhanAlatRepositoryInterface $repo): JsonResponse
    {
        $labIds = array_values(array_filter((array) $request->query('lab_ids', []), 'is_string'));

        try {
            return ApiResponse::success($repo->multiLabFulfillment($labIds));
        } catch (Throwable $e) {
            Log::error('Gagal memuat perbandingan multi-lab', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat perbandingan. Silakan coba lagi.', 500);
        }
    }
}
