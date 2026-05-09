<?php

declare(strict_types=1);

namespace Tests\Unit\Support;

use App\Support\MediaProbe;
use PHPUnit\Framework\TestCase;

final class MediaProbeTest extends TestCase
{
    private string $publicDir;

    protected function setUp(): void
    {
        parent::setUp();
        MediaProbe::resetCache();
        $this->publicDir = sys_get_temp_dir() . '/stagemaster_probe_' . uniqid();
        mkdir($this->publicDir . '/media', 0777, true);
    }

    protected function tearDown(): void
    {
        $this->rrmdir($this->publicDir);
        parent::tearDown();
    }

    public function testDurationReturnsNullForMissingFile(): void
    {
        $this->assertNull(MediaProbe::durationFromAbsolutePath('/no/such/file.mp4'));
    }

    public function testDurationReturnsNullForEmptyPath(): void
    {
        $this->assertNull(MediaProbe::durationFromAbsolutePath(''));
    }

    public function testResolveSafeAbsolutePathRejectsTraversal(): void
    {
        // Create a sibling target outside the publicDir to attempt to escape.
        $outside = dirname($this->publicDir) . '/secret_' . uniqid() . '.txt';
        file_put_contents($outside, 'no peeking');

        try {
            $relative = '../' . basename($outside);
            $resolved = MediaProbe::resolveSafeAbsolutePath($relative, $this->publicDir);
            $this->assertNull($resolved, 'Path traversal must not resolve to a path outside public dir');
        } finally {
            @unlink($outside);
        }
    }

    public function testResolveSafeAbsolutePathAcceptsValidWebPath(): void
    {
        $file = $this->publicDir . '/media/foo.txt';
        file_put_contents($file, 'hi');
        $resolved = MediaProbe::resolveSafeAbsolutePath('/media/foo.txt', $this->publicDir);
        $this->assertSame(realpath($file), $resolved);
    }

    public function testResolveSafeAbsolutePathReturnsNullForMissingFile(): void
    {
        $this->assertNull(MediaProbe::resolveSafeAbsolutePath('/media/does-not-exist.mp4', $this->publicDir));
    }

    public function testResolveSafeAbsolutePathReturnsNullForEmptyInputs(): void
    {
        $this->assertNull(MediaProbe::resolveSafeAbsolutePath('', $this->publicDir));
        $this->assertNull(MediaProbe::resolveSafeAbsolutePath('/media/foo.mp4', ''));
    }

    public function testDurationFromWebPathFallsBackToNullOnInvalidPath(): void
    {
        $this->assertNull(MediaProbe::durationFromWebPath('/../etc/passwd', $this->publicDir));
    }

    private function rrmdir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }
        $items = scandir($dir);
        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;
            if (is_dir($path)) {
                $this->rrmdir($path);
            } else {
                @unlink($path);
            }
        }
        @rmdir($dir);
    }
}
