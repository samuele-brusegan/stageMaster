<?php

namespace Tests\Unit\Models;

use App\Models\Media;
use Tests\TestCase;

class MediaTest extends TestCase
{
    private Media $mediaModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mediaModel = new Media($this->db);
        
        // Create test tables
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
                file_path VARCHAR(255) NOT NULL,
                timestamp_inizio TIME DEFAULT '00:00:00',
                timestamp_fine TIME,
                fade_in_sec INT DEFAULT 0,
                fade_out_sec INT DEFAULT 0,
                ordine_esecuzione INT
            )
        ");
    }

    public function testCreateMedia(): void
    {
        $talentId = $this->insertTestData('talenti', [
            'nome' => 'Test Talent',
            'ordine_scaletta' => 1
        ]);

        $data = [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4',
            'timestamp_inizio' => '00:00:00',
            'timestamp_fine' => '00:03:00',
            'fade_in_sec' => 1,
            'fade_out_sec' => 2,
            'ordine_esecuzione' => 1
        ];

        $id = $this->mediaModel->create($data);
        
        $this->assertIsInt((int)$id);
        $this->assertGreaterThan(0, (int)$id);
        
        $media = $this->mediaModel->find($id);
        $this->assertEquals('/media/test.mp4', $media['file_path']);
        $this->assertEquals('proiettore', $media['tipo_output']);
    }

    public function testCreateMediaWithDefaults(): void
    {
        $talentId = $this->insertTestData('talenti', [
            'nome' => 'Test Talent 2',
            'ordine_scaletta' => 2
        ]);

        $data = [
            'talento_id' => $talentId,
            'tipo_output' => 'gobbo',
            'file_path' => '/media/image.jpg'
        ];

        $id = $this->mediaModel->create($data);
        
        $media = $this->mediaModel->find($id);
        $this->assertEquals('00:00:00', $media['timestamp_inizio']);
        $this->assertEquals(0, $media['fade_in_sec']);
        $this->assertEquals(0, $media['fade_out_sec']);
    }

    public function testFindMedia(): void
    {
        $talentId = $this->insertTestData('talenti', [
            'nome' => 'Test Talent',
            'ordine_scaletta' => 3
        ]);

        $id = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/find.mp4'
        ]);

        $media = $this->mediaModel->find($id);
        
        $this->assertIsArray($media);
        $this->assertEquals($id, $media['id']);
        $this->assertEquals('/media/find.mp4', $media['file_path']);
    }

    public function testFindNonExistentMedia(): void
    {
        $media = $this->mediaModel->find(99999);
        $this->assertFalse($media);
    }

    public function testUpdateMedia(): void
    {
        $talentId = $this->insertTestData('talenti', [
            'nome' => 'Test Talent',
            'ordine_scaletta' => 4
        ]);

        $id = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/original.mp4'
        ]);

        $updateData = [
            'talento_id' => $talentId,
            'tipo_output' => 'gobbo',
            'file_path' => '/media/updated.jpg',
            'timestamp_inizio' => '00:01:00',
            'timestamp_fine' => '00:05:00',
            'fade_in_sec' => 3,
            'fade_out_sec' => 4,
            'ordine_esecuzione' => 2
        ];

        $result = $this->mediaModel->update($id, $updateData);
        
        $this->assertTrue($result);
        
        $media = $this->mediaModel->find($id);
        $this->assertEquals('/media/updated.jpg', $media['file_path']);
        $this->assertEquals('gobbo', $media['tipo_output']);
        $this->assertEquals('00:01:00', $media['timestamp_inizio']);
    }

    public function testDeleteMedia(): void
    {
        $talentId = $this->insertTestData('talenti', [
            'nome' => 'Test Talent',
            'ordine_scaletta' => 5
        ]);

        $id = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/delete.mp4'
        ]);

        $result = $this->mediaModel->delete($id);
        
        $this->assertTrue($result);
        
        $media = $this->mediaModel->find($id);
        $this->assertFalse($media);
    }

    public function testGetByTalento(): void
    {
        $talentId = $this->insertTestData('talenti', [
            'nome' => 'Test Talent',
            'ordine_scaletta' => 6
        ]);

        $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/first.mp4',
            'ordine_esecuzione' => 1
        ]);

        $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'gobbo',
            'file_path' => '/media/second.jpg',
            'ordine_esecuzione' => 2
        ]);

        $mediaList = $this->mediaModel->getByTalento($talentId);
        
        $this->assertIsArray($mediaList);
        $this->assertCount(2, $mediaList);
        $this->assertEquals('/media/first.mp4', $mediaList[0]['file_path']);
        $this->assertEquals('/media/second.jpg', $mediaList[1]['file_path']);
    }

    public function testGetByTalentoEmpty(): void
    {
        $talentId = $this->insertTestData('talenti', [
            'nome' => 'Empty Talent',
            'ordine_scaletta' => 7
        ]);

        $mediaList = $this->mediaModel->getByTalento($talentId);
        
        $this->assertIsArray($mediaList);
        $this->assertEmpty($mediaList);
    }

    public function testGetByTalentoNonExistent(): void
    {
        $mediaList = $this->mediaModel->getByTalento(99999);
        
        $this->assertIsArray($mediaList);
        $this->assertEmpty($mediaList);
    }
}
