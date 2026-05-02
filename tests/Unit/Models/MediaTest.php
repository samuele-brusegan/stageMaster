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
        $this->assertEquals('VIDEO', $media['tipo_media']);
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
        $this->assertNull($media['screen_id']);
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

    public function testGetAllReturnsTalentAndScreenNames(): void
    {
        $talentId = $this->insertTestData('talenti', [
            'nome' => 'Talent With Screen',
            'ordine_scaletta' => 4
        ]);
        $screenId = $this->insertTestData('screens', [
            'nome' => 'Main Screen',
            'tipo' => 'indipendente'
        ]);

        $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'screen_id' => $screenId,
            'file_path' => '/media/list.jpg',
            'tipo_media' => 'FOTO'
        ]);

        $media = $this->mediaModel->getAll();

        $this->assertCount(1, $media);
        $this->assertEquals('Talent With Screen', $media[0]['talento_nome']);
        $this->assertEquals('Main Screen', $media[0]['screen_nome']);
    }

    public function testExistsForTalentoDetectsDuplicateFile(): void
    {
        $talentId = $this->insertTestData('talenti', [
            'nome' => 'Duplicate Talent',
            'ordine_scaletta' => 5
        ]);

        $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/duplicate.jpg',
            'tipo_media' => 'FOTO'
        ]);

        $this->assertTrue($this->mediaModel->existsForTalento($talentId, '/media/duplicate.jpg'));
        $this->assertFalse($this->mediaModel->existsForTalento($talentId, '/media/other.jpg'));
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
            'friendly_name' => 'Updated Friendly',
            'screen_id' => null,
            'tipo_media' => 'FOTO',
            'timestamp_inizio' => '00:01:00',
            'timestamp_fine' => '00:05:00',
            'durata_totale_sec' => 240,
            'fade_in_sec' => 3,
            'fade_out_sec' => 4,
            'ordine_esecuzione' => 2
        ];

        $result = $this->mediaModel->update($id, $updateData);
        
        $this->assertTrue($result);
        
        $media = $this->mediaModel->find($id);
        $this->assertEquals('/media/updated.jpg', $media['file_path']);
        $this->assertEquals('Updated Friendly', $media['friendly_name']);
        $this->assertEquals('gobbo', $media['tipo_output']);
        $this->assertEquals('00:01:00', $media['timestamp_inizio']);
    }

    public function testUpdateTimelineMedia(): void
    {
        $talentId = $this->insertTestData('talenti', [
            'nome' => 'Timeline Talent',
            'ordine_scaletta' => 6
        ]);
        $screenId = $this->insertTestData('screens', [
            'nome' => 'Timeline Screen',
            'tipo' => 'indipendente'
        ]);
        $id = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/timeline.jpg',
            'tipo_media' => 'FOTO'
        ]);

        $result = $this->mediaModel->updateTimelineMedia($id, [
            'friendly_name' => 'Timeline Friendly',
            'screen_id' => $screenId,
            'timestamp_inizio' => '00:00:10',
            'timestamp_fine' => '00:00:25',
            'durata_totale_sec' => 15,
            'ordine_esecuzione' => 3
        ]);

        $this->assertTrue($result);
        $media = $this->mediaModel->find($id);
        $this->assertEquals('Timeline Friendly', $media['friendly_name']);
        $this->assertEquals($screenId, $media['screen_id']);
        $this->assertEquals(15, $media['durata_totale_sec']);
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
