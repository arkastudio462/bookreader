<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Storage\BookStorage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorageTestController extends Controller
{
    /**
     * Endpoint uji coba upload ke Cloudflare R2 (PRD Fase 1 DoD).
     * POST /admin/storage/test — hanya role:admin.
     */
    public function store(Request $request, BookStorage $storage): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'max:102400'], // 100 MB
        ]);

        $file = $request->file('file');

        if (!$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak valid.',
            ], 422);
        }

        try {
            $s3Key = $storage->storeBook($file, $this->detectType($file->getClientOriginalExtension()));

            return response()->json([
                'success' => true,
                'message' => 'Upload ke R2 berhasil.',
                'data' => [
                    's3_key' => $s3Key,
                    'size' => $file->getSize(),
                    'mime' => $file->getMimeType(),
                    'uploaded_by' => Auth::id(),
                    'temporary_url' => $storage->temporaryUrl($s3Key, 10),
                ],
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Upload gagal: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Hapus file uji (admin only).
     */
    public function destroy(Request $request, BookStorage $storage): JsonResponse
    {
        $request->validate(['s3_key' => ['required', 'string']]);

        $deleted = $storage->delete($request->input('s3_key'));

        return response()->json([
            'success' => $deleted,
            'message' => $deleted ? 'File dihapus dari R2.' : 'File tidak ditemukan.',
        ], $deleted ? 200 : 404);
    }

    private function detectType(string $ext): string
    {
        return strtolower($ext) === 'pdf' ? 'pdf' : 'epub';
    }
}
