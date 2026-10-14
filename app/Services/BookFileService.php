<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BookFileService
{
    protected string $disk;

    public function __construct(string $disk = 'r2')
    {
        $this->disk = $disk;
    }

    /**
     * Upload cover image buku.
     */
    public function uploadCover(UploadedFile $file): string
    {
        $path = 'covers/' . Str::uuid() . '.' . $file->guessExtension();

        Storage::disk($this->disk)->putFileAs(
            dirname($path),
            $file,
            basename($path),
            ['visibility' => 'public']
        );

        return $path;
    }

    /**
     * Upload file buku (EPUB/PDF).
     */
    public function uploadBookFile(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if (!in_array($extension, ['epub', 'pdf'])) {
            throw new \InvalidArgumentException('Format file harus EPUB atau PDF.');
        }

        $path = 'books/' . Str::uuid() . '.' . $extension;

        Storage::disk($this->disk)->put($path, file_get_contents($file->getRealPath()));

        return $path;
    }

    /**
     * Tentukan tipe file buku dari path (epub|pdf).
     */
    public function fileType(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        return in_array($extension, ['epub', 'pdf']) ? $extension : null;
    }

    /**
     * Cek keberadaan file, tahan terhadap error koneksi storage.
     */
    public function exists(string $path): bool
    {
        try {
            return Storage::disk($this->disk)->exists($path);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Generate temporary URL untuk membaca buku (30 menit).
     * Fallback ke public URL bila driver tidak mendukung signed URL.
     */
    public function getTemporaryUrl(string $path, int $minutes = 30): ?string
    {
        if (!$this->exists($path)) {
            return null;
        }

        try {
            if (method_exists(Storage::disk($this->disk), 'temporaryUrl')) {
                return Storage::disk($this->disk)->temporaryUrl($path, now()->addMinutes($minutes));
            }
        } catch (\Throwable $e) {
            // lanjut ke fallback
        }

        try {
            return Storage::disk($this->disk)->url($path);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Hapus file dari storage.
     */
    public function delete(string $path): bool
    {
        if ($this->exists($path)) {
            return Storage::disk($this->disk)->delete($path);
        }
        return false;
    }
}
