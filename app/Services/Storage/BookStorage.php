<?php

namespace App\Services\Storage;

use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BookStorage
{
    protected string $disk = 'r2';
    protected int $urlExpiryMinutes = 5;

    /**
     * Simpan file ke R2 dan return s3_key.
     */
    public function storeBook(object $file, string $type = 'epub'): string
    {
        $filename = $this->generateFilename($type);
        $path = "books/{$filename}";
        
        Storage::disk($this->disk)->putFileAs(
            'books',
            $file,
            $filename,
            'private'
        );

        return $path;
    }

    /**
     * Simpan cover ke R2.
     */
    public function storeCover(object $file, int $bookId): string
    {
        $filename = "cover-{$bookId}-" . time() . '.' . $file->getClientOriginalExtension();
        $path = "covers/{$filename}";
        
        Storage::disk($this->disk)->putFileAs(
            'covers',
            $file,
            $filename,
            'private'
        );

        return $path;
    }

    /**
     * Buat temporary URL untuk akses pembaca (server-side only).
     */
    public function temporaryUrl(string $s3Key, int $expiryMinutes = null): string
    {
        $expiry = $expiryMinutes ?? $this->urlExpiryMinutes;
        
        return Storage::disk($this->disk)->temporaryUrl(
            $s3Key,
            now()->addMinutes($expiry)
        );
    }

    /**
     * Hapus file dari R2.
     */
    public function delete(string $s3Key): bool
    {
        return Storage::disk($this->disk)->delete($s3Key);
    }

    /**
     * Check keberadaan file.
     */
    public function exists(string $s3Key): bool
    {
        return Storage::disk($this->disk)->exists($s3Key);
    }

    /**
     * Validasi magic bytes (EPUB = ZIP, PDF = PDF header).
     */
    public static function validateMagicBytes(string $filePath, string $type): bool
    {
        if (!file_exists($filePath)) {
            return false;
        }

        $handle = fopen($filePath, 'rb');
        if (!$handle) {
            return false;
        }

        $bytes = fread($handle, 4);
        fclose($handle);

        // EPUB = ZIP header (50 4B 03 04)
        // PDF = %PDF header
        if ($type === 'epub') {
            return strpos($bytes, 'PK') === 0; // ZIP magic
        } elseif ($type === 'pdf') {
            return strpos($bytes, '%PDF') === 0;
        }

        return false;
    }

    /**
     * Validasi file size.
     */
    public static function validateSize(object $file, int $maxMB = 100): bool
    {
        return $file->getSize() <= ($maxMB * 1024 * 1024);
    }

    protected function generateFilename(string $type): string
    {
        $ext = $type === 'pdf' ? 'pdf' : 'epub';
        return sprintf(
            'book_%s_%d.%s',
            now()->format('YmdHis'),
            random_int(1000, 9999),
            $ext
        );
    }
}
