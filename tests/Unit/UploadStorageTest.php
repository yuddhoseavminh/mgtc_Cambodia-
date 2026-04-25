<?php

namespace Tests\Unit;

use App\Support\UploadStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UploadStorageTest extends TestCase
{
    private string $testRoot;

    private string $uploadsRoot;

    private string $legacyRoot;

    protected function setUp(): void
    {
        parent::setUp();

        $this->testRoot = storage_path('framework/testing/upload-storage');
        $this->uploadsRoot = $this->testRoot.'/uploads';
        $this->legacyRoot = $this->testRoot.'/legacy';

        File::deleteDirectory($this->testRoot);
        File::ensureDirectoryExists($this->uploadsRoot);
        File::ensureDirectoryExists($this->legacyRoot);

        Config::set('filesystems.uploads_disk', 'uploads');
        Config::set('filesystems.legacy_uploads_disk', 'local');
        Config::set('filesystems.disks.uploads', [
            'driver' => 'local',
            'root' => $this->uploadsRoot,
            'throw' => false,
        ]);
        Config::set('filesystems.disks.local', [
            'driver' => 'local',
            'root' => $this->legacyRoot,
            'throw' => false,
        ]);

        Storage::forgetDisk(['uploads', 'local']);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->testRoot);

        parent::tearDown();
    }

    public function test_it_reads_files_from_legacy_disk_when_needed(): void
    {
        File::put($this->legacyRoot.'/legacy.txt', 'legacy');

        $this->assertTrue(UploadStorage::exists('legacy.txt'));
        $this->assertSame($this->legacyRoot.DIRECTORY_SEPARATOR.'legacy.txt', UploadStorage::path('legacy.txt'));
    }

    public function test_it_deletes_files_from_both_primary_and_legacy_disks(): void
    {
        File::put($this->uploadsRoot.'/shared.txt', 'primary');
        File::put($this->legacyRoot.'/shared.txt', 'legacy');

        $this->assertTrue(UploadStorage::delete('shared.txt'));
        $this->assertFileDoesNotExist($this->uploadsRoot.'/shared.txt');
        $this->assertFileDoesNotExist($this->legacyRoot.'/shared.txt');
    }

    public function test_it_creates_missing_upload_directories_before_storing(): void
    {
        File::deleteDirectory($this->uploadsRoot);

        $path = UploadStorage::storeAs(
            UploadedFile::fake()->create('report.pdf', 10, 'application/pdf'),
            'nested/uploads',
            'stored-report.pdf',
        );

        $this->assertSame('nested/uploads/stored-report.pdf', $path);
        $this->assertFileExists($this->uploadsRoot.'/nested/uploads/stored-report.pdf');
    }
}
