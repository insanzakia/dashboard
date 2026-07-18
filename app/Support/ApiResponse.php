<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

/**
 * Envelope JSON konsisten untuk endpoint API publik (dashboard-data/*, wilayah/*).
 * Bentuk selalu: { success, message, data, errors } — memudahkan konsumsi & error-handling di frontend.
 *
 * CATATAN: Envelope ini TIDAK dipakai untuk controller admin berbasis Inertia.
 * Inertia mengandalkan format redirect + 422 native Laravel; membungkusnya akan
 * merusak auto-handling error di sisi React.
 */
class ApiResponse
{
    public static function success(mixed $data = null, string $message = 'OK', int $status = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ], $status);
    }

    /**
     * @param  array<string, mixed>|null  $errors
     */
    public static function error(string $message, int $status = 400, ?array $errors = null): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $status);
    }
}
