<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\DashboardRepositoryInterface;
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
    public function index(): Response
    {
        return Inertia::render('Dashboard/Index');
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
}
