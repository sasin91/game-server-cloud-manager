<?php

namespace App\Filesystem;

use function app_path;
use function base_path;
use function file_exists;
use function str_replace;
use function ucwords;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Fluent;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FileIndex
{
    /**
     * Scan the given path for PHP classes
     *
     * @param string|null $path
     * @param string $extension
     * @return Collection
     */
    public static function scan($path = null, $extension = 'php')
    {
        $path = file_exists($path) ? $path : app_path($path);

        if (Str::startsWith($extension, '.') === false) {
            $extension = '.'.$extension;
        }

        return Collection::make(
            Finder::create()
                ->files()
                ->in($path)
                ->ignoreDotFiles(true)
                ->name('*'.$extension)
        )->map(function (SplFileInfo $file) use ($extension) {
            $path = Str::after($file->getPathname(), base_path() . DIRECTORY_SEPARATOR);

            // Replace / and .php with \\
            $className = str_replace(
                ['/', $extension],
                ['\\', ''],
                $path
            );

            return new Fluent([
                'file' => $path,
                'name' => $file->getFilenameWithoutExtension(),
                'class' => ucwords($className)
            ]);
        })
        ->values();
    }
}
