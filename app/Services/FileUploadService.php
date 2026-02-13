<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class FileUploadService
{
    /**
     * Upload file dengan validasi dan generate nama unik.
     *
     * @param  string  $folder  Folder tujuan (contoh: 'products', 'attachments/outbound')
     * @param  string  $disk  Disk storage (default: 'public')
     * @param  array  $allowedMimes  Array MIME types yang diizinkan
     * @param  int  $maxSize  Ukuran maksimal dalam KB (default: 2048 = 2MB)
     * @param  string|null  $prefix  Prefix untuk nama file
     * @return string Path file yang diupload
     *
     * @throws ValidationException
     */
    public function upload(
        UploadedFile $file,
        string $folder,
        string $disk = 'public',
        array $allowedMimes = [],
        int $maxSize = 2048,
        ?string $prefix = null
    ): string {
        // Validasi file
        $this->validateFile($file, $allowedMimes, $maxSize);

        // Generate nama file unik
        $filename = $this->generateUniqueFilename($file, $prefix);

        // Store file
        $path = $file->storeAs($folder, $filename, $disk);

        if (! $path) {
            throw ValidationException::withMessages([
                'file' => 'Gagal mengupload file. Silakan coba lagi.',
            ]);
        }

        return $path;
    }

    /**
     * Upload multiple files.
     *
     * @param  array<UploadedFile>  $files
     * @return array<string> Array path files yang diupload
     *
     * @throws ValidationException
     */
    public function uploadMultiple(
        array $files,
        string $folder,
        string $disk = 'public',
        array $allowedMimes = [],
        int $maxSize = 2048,
        ?string $prefix = null
    ): array {
        $uploadedPaths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $uploadedPaths[] = $this->upload($file, $folder, $disk, $allowedMimes, $maxSize, $prefix);
            }
        }

        return $uploadedPaths;
    }

    /**
     * Delete file dari storage.
     */
    public function delete(string $path, string $disk = 'public'): bool
    {
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->delete($path);
        }

        return true; // File tidak ada, anggap berhasil
    }

    /**
     * Get URL file untuk public access.
     */
    public function getUrl(string $path, string $disk = 'public'): string
    {
        return Storage::disk($disk)->url($path);
    }

    /**
     * Get file size dalam format human readable.
     */
    public function getFileSize(string $path, string $disk = 'public'): string
    {
        $size = Storage::disk($disk)->size($path);

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }

        return round($size, 2).' '.$units[$i];
    }

    /**
     * Validasi file upload.
     *
     *
     * @throws ValidationException
     */
    private function validateFile(UploadedFile $file, array $allowedMimes, int $maxSize): void
    {
        $errors = [];

        // Check if file is valid
        if (! $file->isValid()) {
            $errors['file'] = 'File yang diupload tidak valid.';
        }

        // Check file size
        if ($file->getSize() > $maxSize * 1024) {
            $errors['file'] = "Ukuran file maksimal adalah {$maxSize}KB.";
        }

        // Check MIME type
        if (! empty($allowedMimes) && ! in_array($file->getMimeType(), $allowedMimes)) {
            $allowedExtensions = implode(', ', array_map(function ($mime) {
                return $this->mimeToExtension($mime);
            }, $allowedMimes));
            $errors['file'] = "Tipe file tidak diizinkan. File yang diizinkan: {$allowedExtensions}";
        }

        if (! empty($errors)) {
            throw ValidationException::withMessages($errors);
        }
    }

    /**
     * Generate nama file unik.
     */
    private function generateUniqueFilename(UploadedFile $file, ?string $prefix = null): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = time();
        $random = uniqid();

        $prefix = $prefix ? $prefix.'_' : '';

        return $prefix.$timestamp.'_'.$random.'.'.$extension;
    }

    /**
     * Convert MIME type to file extension.
     */
    private function mimeToExtension(string $mime): string
    {
        $mimeMap = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'text/csv' => 'csv',
            'text/plain' => 'txt',
        ];

        return $mimeMap[$mime] ?? 'unknown';
    }

    /**
     * Get predefined validation rules untuk berbagai jenis file.
     */
    public static function getValidationRules(): array
    {
        return [
            'images' => [
                'mimes' => ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
                'max_size' => 2048, // 2MB
            ],
            'documents' => [
                'mimes' => [
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/csv',
                    'text/plain',
                ],
                'max_size' => 5120, // 5MB
            ],
            'all' => [
                'mimes' => [
                    'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/csv',
                    'text/plain',
                ],
                'max_size' => 5120, // 5MB
            ],
        ];
    }
}
