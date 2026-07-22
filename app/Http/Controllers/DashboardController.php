<?php

namespace App\Http\Controllers;

use App\Models\Labkesmas;
use App\Repositories\Contracts\DashboardRepositoryInterface;
use App\Repositories\Contracts\InventarisAlatRepositoryInterface;
use App\Support\ApiResponse;
use App\Support\DashboardFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

/**
 * Thin controller: menerima request, mendelegasikan ke Repository, mengembalikan response.
 * Tidak ada logika query di sini.
 */
class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardRepositoryInterface $dashboard,
    ) {}

    /** Halaman dashboard publik (Inertia). */
    public function index(InventarisAlatRepositoryInterface $repo): Response
    {
        return Inertia::render('Dashboard/Index', [
            'labkesmasOptions' => $repo->labkesmasOptions(),
        ]);
    }

    /** Endpoint JSON: kartu ringkasan sesuai filter. */
    public function summary(Request $request): JsonResponse
    {
        try {
            $data = $this->dashboard->summary(DashboardFilter::fromRequest($request));

            return ApiResponse::success($data);
        } catch (Throwable $e) {
            Log::error('Gagal memuat ringkasan dashboard', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat ringkasan. Silakan coba lagi.', 500);
        }
    }

    /** Endpoint JSON: deret tren bulanan sesuai filter. */
    public function trend(Request $request): JsonResponse
    {
        try {
            $data = $this->dashboard->trend(DashboardFilter::fromRequest($request));

            return ApiResponse::success($data);
        } catch (Throwable $e) {
            Log::error('Gagal memuat tren dashboard', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat tren. Silakan coba lagi.', 500);
        }
    }

    /** Endpoint JSON: deret tren bulanan per jenis pemeriksaan terpilih. */
    public function trendByJenis(Request $request): JsonResponse
    {
        try {
            $jenisIds = array_values(array_filter((array) $request->query('jenis_ids', []), 'is_string'));
            $data = $this->dashboard->trendByJenis(DashboardFilter::fromRequest($request), $jenisIds);

            return ApiResponse::success($data);
        } catch (Throwable $e) {
            Log::error('Gagal memuat tren per jenis pemeriksaan', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat tren per jenis pemeriksaan. Silakan coba lagi.', 500);
        }
    }

    /** Endpoint JSON: opsi jenis pemeriksaan untuk checklist tren. */
    public function jenisPemeriksaan(): JsonResponse
    {
        try {
            $data = $this->dashboard->jenisPemeriksaanOptions();

            return ApiResponse::success($data);
        } catch (Throwable $e) {
            Log::error('Gagal memuat opsi jenis pemeriksaan', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat opsi jenis pemeriksaan. Silakan coba lagi.', 500);
        }
    }

    /** Endpoint JSON: deret tren bulanan dikelompokkan menurut provinsi/regional/tier. */
    public function trendGrouped(Request $request): JsonResponse
    {
        $allowed = ['provinsi', 'regional', 'tier'];
        $groupBy = in_array($request->query('group_by'), $allowed, true) ? $request->query('group_by') : 'tier';

        try {
            $data = $this->dashboard->trendGrouped(DashboardFilter::fromRequest($request), $groupBy);

            return ApiResponse::success($data);
        } catch (Throwable $e) {
            Log::error('Gagal memuat tren terkelompok', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat tren terkelompok. Silakan coba lagi.', 500);
        }
    }

    /** Endpoint JSON: deret tren bulanan untuk beberapa Labkesmas terpilih. */
    public function trendMultiLabkesmas(Request $request): JsonResponse
    {
        $labIds = array_values(array_filter((array) $request->query('lab_ids', []), 'is_string'));

        try {
            $data = $this->dashboard->trendMultiLabkesmas($labIds);

            return ApiResponse::success($data);
        } catch (Throwable $e) {
            Log::error('Gagal memuat tren multi-labkesmas', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat tren multi-labkesmas. Silakan coba lagi.', 500);
        }
    }

    /** Endpoint JSON: ringkasan pemeriksaan satu Labkesmas (total + per jenis + tren) untuk halaman profil. */
    public function labPemeriksaan(Labkesmas $labkesmas): JsonResponse
    {
        try {
            $data = $this->dashboard->labPemeriksaan($labkesmas->id);

            return ApiResponse::success($data);
        } catch (Throwable $e) {
            Log::error('Gagal memuat pemeriksaan lab', ['error' => $e->getMessage()]);

            return ApiResponse::error('Gagal memuat data pemeriksaan lab. Silakan coba lagi.', 500);
        }
    }
}
