<?php

namespace App\Support;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class UploadStorage
{
    /**
     * @var array<string, true>
     */
    private static array $ensuredDirectories = [];

    public static function diskName(): string
    {
        $disk = (string) config('filesystems.uploads_disk', 'uploads');
        $legacyDisk = self::legacyDiskName();

        if (app()->environment('testing') && $disk !== $legacyDisk) {
            $legacyRoot = str_replace('\\', '/', Storage::disk($legacyDisk)->path(''));
            $fakeRootSuffix = '/framework/testing/disks/'.$legacyDisk;

            if (str_ends_with(rtrim($legacyRoot, '/'), $fakeRootSuffix)) {
                return $legacyDisk;
            }
        }

        return $disk;
    }

    public static function legacyDiskName(): string
    {
        return (string) config('filesystems.legacy_uploads_disk', 'local');
    }

    public static function disk(): FilesystemAdapter
    {
        return Storage::disk(self::diskName());
    }

    public static function store(UploadedFile $file, string $directory): string
    {
        self::ensureDirectoryExists($directory);

        $path = $file->store($directory, self::diskName());

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('Unable to store uploaded file.');
        }

        return $path;
    }

    public static function storeAs(UploadedFile $file, string $directory, string $name): string
    {
        self::ensureDirectoryExists($directory);

        $path = $file->storeAs($directory, $name, self::diskName());

        if (! is_string($path) || $path === '') {
            throw new RuntimeException('Unable to store uploaded file.');
        }

        return $path;
    }

    public static function exists(?string $path): bool
    {
        if (! filled($path)) {
            return false;
        }

        if (self::disk()->exists($path)) {
            return true;
        }

        return self::legacyDiskName() !== self::diskName()
            && Storage::disk(self::legacyDiskName())->exists($path);
    }

    public static function readDisk(?string $path): FilesystemAdapter
    {
        if (filled($path) && self::disk()->exists($path)) {
            return self::disk();
        }

        if (filled($path) && self::legacyDiskName() !== self::diskName()) {
            $legacyDisk = Storage::disk(self::legacyDiskName());

            if ($legacyDisk->exists($path)) {
                return $legacyDisk;
            }
        }

        return self::disk();
    }

    public static function path(string $path): string
    {
        return self::readDisk($path)->path($path);
    }

    public static function size(?string $path): ?int
    {
        if (! self::exists($path)) {
            return null;
        }

        return (int) self::readDisk($path)->size($path);
    }

    public static function delete(string|array|null $paths): bool
    {
        $paths = array_values(array_filter((array) $paths, fn (mixed $path) => filled($path)));

        if ($paths === []) {
            return false;
        }

        $deleted = self::disk()->delete($paths);

        if (self::legacyDiskName() === self::diskName()) {
            return $deleted;
        }

        return Storage::disk(self::legacyDiskName())->delete($paths) || $deleted;
    }

    private static function ensureDirectoryExists(string $directory): void
    {
        $diskName = self::diskName();

        if (! self::isLocalDisk($diskName)) {
            return;
        }

        $disk = Storage::disk($diskName);
        $targetDirectory = trim($directory, '/');
        $rootPath = str_replace('\\', '/', rtrim($disk->path(''), '/'));
        $cacheKey = $rootPath.'|'.$targetDirectory;

        if (isset(self::$ensuredDirectories[$cacheKey])) {
            return;
        }

        $absolutePath = $targetDirectory === ''
            ? $disk->path('.')
            : $disk->path($targetDirectory);

        File::ensureDirectoryExists($absolutePath);
        self::$ensuredDirectories[$cacheKey] = true;
    }

    private static function isLocalDisk(string $diskName): bool
    {
        return (string) config("filesystems.disks.{$diskName}.driver", 'local') === 'local';
    }
}
