<?php

namespace Tests\Unit\Controllers;

use App\Controllers\MediaController;
use App\Models\Media;
use App\Models\MediaLibrary;
use Tests\TestCase;

/**
 * Test-only subclass that exposes the protected resolveDefaultDuration helper
 * and skips the DB-connecting constructor.
 */
final class TestableMediaController extends MediaController
{
    public function __construct() { /* bypass DB bootstrap */ }

    public function exposeResolveDefaultDuration(MediaLibrary $library, array $row, string $type): ?int
    {
        return $this->resolveDefaultDuration($library, $row, $type);
    }
}

class MediaControllerTest extends TestCase
{
    private Media $mediaModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->executeSql("
            CREATE TABLE IF NOT EXISTS talenti (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                ordine_scaletta INT
            )
        ");
        
        $this->executeSql("
            CREATE TABLE IF NOT EXISTS media_performance (
                id INT AUTO_INCREMENT PRIMARY KEY,
                talento_id INT,
                tipo_output ENUM('proiettore', 'gobbo') NOT NULL,
                screen_id INT NULL,
                file_path VARCHAR(255) NOT NULL,
                friendly_name VARCHAR(100) NULL,
                tipo_media ENUM('VIDEO', 'AUDIO', 'FOTO') DEFAULT 'VIDEO',
                timestamp_inizio TIME DEFAULT '00:00:00',
                timestamp_fine TIME,
                durata_totale_sec INT NULL,
                fade_in_sec INT DEFAULT 0,
                fade_out_sec INT DEFAULT 0,
                ordine_esecuzione INT
            )
        ");

        $this->executeSql("
            CREATE TABLE IF NOT EXISTS screens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(50) NOT NULL,
                tipo ENUM('indipendente', 'mirror') DEFAULT 'indipendente',
                screen_riferimento_id INT NULL
            )
        ");
        
        $this->mediaModel = new Media($this->db);
    }

    public function testGetByTalentoReturnsMediaList(): void
    {
        $talentId = $this->insertTestData('talenti', [
            'nome' => 'Test Talent',
            'ordine_scaletta' => 1
        ]);

        $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test1.mp4',
            'ordine_esecuzione' => 1
        ]);

        $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'gobbo',
            'file_path' => '/media/test2.jpg',
            'ordine_esecuzione' => 2
        ]);

        $result = $this->mediaModel->getByTalento($talentId);
        
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testGetByTalentoNonExistent(): void
    {
        $result = $this->mediaModel->getByTalento(99999);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function testShowReturnsMedia(): void
    {
        $talentId = $this->insertTestData('talenti', [
            'nome' => 'Test Talent',
            'ordine_scaletta' => 1
        ]);

        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        $result = $this->mediaModel->find($mediaId);
        
        $this->assertIsArray($result);
        $this->assertEquals($mediaId, $result['id']);
        $this->assertEquals('/media/test.mp4', $result['file_path']);
    }

    public function testShowNonExistentReturnsFalse(): void
    {
        $result = $this->mediaModel->find(99999);
        $this->assertFalse($result);
    }

    public function testResolveDefaultDurationUsesCachedValueForVideo(): void
    {
        $this->ensureMediaLibraryTable();
        $library = new MediaLibrary($this->db);
        $controller = new TestableMediaController();
        $row = [
            'id' => 1,
            'file_path' => '/media/anything.mp4',
            'duration_sec' => 240,
        ];
        $this->assertSame(240, $controller->exposeResolveDefaultDuration($library, $row, 'VIDEO'));
    }

    public function testResolveDefaultDurationReturnsNullForFotoEvenIfCached(): void
    {
        $this->ensureMediaLibraryTable();
        $library = new MediaLibrary($this->db);
        $controller = new TestableMediaController();
        $row = [
            'id' => 1,
            'file_path' => '/media/anything.jpg',
            'duration_sec' => 999,
        ];
        $this->assertNull($controller->exposeResolveDefaultDuration($library, $row, 'FOTO'));
    }

    public function testResolveDefaultDurationReturnsNullWhenLibraryHasNoCacheAndFileMissing(): void
    {
        $this->ensureMediaLibraryTable();
        $library = new MediaLibrary($this->db);
        $controller = new TestableMediaController();
        $row = [
            'id' => 1,
            'file_path' => '/media/missing-file-' . uniqid() . '.mp4',
            'duration_sec' => null,
        ];
        $this->assertNull($controller->exposeResolveDefaultDuration($library, $row, 'AUDIO'));
    }

    public function testResolveDefaultDurationCoercesFloatToInt(): void
    {
        $this->ensureMediaLibraryTable();
        $library = new MediaLibrary($this->db);
        $controller = new TestableMediaController();
        $row = ['id' => 1, 'file_path' => '/media/x.mp4', 'duration_sec' => 12.7];
        $this->assertSame(13, $controller->exposeResolveDefaultDuration($library, $row, 'VIDEO'));
    }

    private function ensureMediaLibraryTable(): void
    {
        $this->executeSql("
            CREATE TABLE IF NOT EXISTS media (
                id INT AUTO_INCREMENT PRIMARY KEY,
                file_name VARCHAR(255) NOT NULL,
                file_path VARCHAR(255) NOT NULL,
                file_type ENUM('VIDEO', 'AUDIO', 'FOTO') NOT NULL,
                file_size INT,
                duration_sec INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }
}
