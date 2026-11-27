<?php

namespace App\Http\Controllers\Admin\Concerns;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

trait StoresPublicUploads
{
    protected function storePublicImage(UploadedFile $file, string $subdirectory): string
    {
        $directory = public_path($subdirectory);

        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = Str::uuid()->toString().'.'.$extension;
        $file->move($directory, $filename);

        return '/'.$subdirectory.'/'.$filename;
    }

    protected function deletePublicPath(?string $path): void
    {
        if ($path === null || $path === '') {
            return;
        }

        if (! str_starts_with($path, '/')) {
            return;
        }

        $full = public_path(ltrim($path, '/'));

        if (is_file($full)) {
            unlink($full);
        }
    }
}