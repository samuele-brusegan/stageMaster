<?php

namespace Tests\Unit\Controllers;

use App\Models\Media;
use Tests\TestCase;

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
}
