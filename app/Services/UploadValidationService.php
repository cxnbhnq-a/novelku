<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class UploadValidationService
{
    public static function imageRules(): array
    {
        return [
            'nullable',
            'file',
            'image',
            'mimes:jpeg,png,jpg,webp',
            'max:2048',
            function ($attribute, $value, $fail) {
                if (! $value instanceof UploadedFile) {
                    return;
                }

                $originalName = strtolower($value->getClientOriginalName());
                $extension = strtolower($value->getClientOriginalExtension());
                $allowedExtensions = ['jpeg', 'jpg', 'png', 'webp'];
                $dangerousExtensions = [
                    'php', 'phtml', 'php3', 'php4', 'php5', 'phps',
                    'exe', 'sh', 'bash', 'pl', 'py', 'js', 'jsp',
                    'asp', 'aspx', 'cgi', 'dll', 'bin', 'cmd',
                ];

                if (! in_array($extension, $allowedExtensions, true)) {
                    $fail('Format file tidak aman. Gunakan JPG, PNG, atau WEBP.');
                    return;
                }

                $nameParts = explode('.', $originalName);
                if (count($nameParts) > 2) {
                    $extraParts = array_slice($nameParts, 0, -1);
                    foreach ($extraParts as $part) {
                        if (in_array($part, $dangerousExtensions, true)) {
                            $fail('Format file tidak aman (Double extension terlarang).');
                            return;
                        }
                    }
                }

                $mimeType = $value->getMimeType();
                if (! in_array($mimeType, ['image/jpeg', 'image/png', 'image/webp'], true)) {
                    $fail('Tipe file tidak valid. Pastikan ini adalah gambar yang aman.');
                    return;
                }

                try {
                    $imageInfo = @getimagesize($value->getRealPath());
                    if (! is_array($imageInfo) || empty($imageInfo[2])) {
                        $fail('Isi file tidak valid. Pastikan file adalah gambar.');
                    }
                } catch (\Throwable $ex) {
                    $fail('Isi file tidak valid. Pastikan file adalah gambar.');
                }
            }
        ];
    }
}
