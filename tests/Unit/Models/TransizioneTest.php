<?php

namespace Tests\Unit\Models;

use App\Models\Transizione;
use Tests\TestCase;

class TransizioneTest extends TestCase
{
    private Transizione $transizioneModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transizioneModel = new Transizione($this->db);
        
        $this->executeSql("
            CREATE TABLE IF NOT EXISTS talenti (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL
            )
        ");
        
        $this->executeSql("
            CREATE TABLE IF NOT EXISTS media_performance (
                id INT AUTO_INCREMENT PRIMARY KEY,
                talento_id INT,
                tipo_output ENUM('proiettore', 'gobbo') NOT NULL,
                file_path VARCHAR(255) NOT NULL
            )
        ");
        
        $this->executeSql("
            CREATE TABLE IF NOT EXISTS transizioni (
                id INT AUTO_INCREMENT PRIMARY KEY,
                media_id INT NOT NULL,
                tipo_dissolvenza ENUM('fade_to_black', 'fade_from_black', 'crossfade', 'cut', 'dissolve') DEFAULT 'fade_to_black',
                durata_sec DECIMAL(5,2) DEFAULT 0.00,
                offset_prima_sec DECIMAL(5,2) DEFAULT 0.00,
                offset_dopo_sec DECIMAL(5,2) DEFAULT 0.00
            )
        ");
    }

    public function testCreateTransizione(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        $data = [
            'media_id' => $mediaId,
            'tipo_dissolvenza' => 'fade_to_black',
            'durata_sec' => 1.5,
            'offset_prima_sec' => 0.5,
            'offset_dopo_sec' => 0.3
        ];

        $id = $this->transizioneModel->create($data);
        
        $this->assertIsInt((int)$id);
        $this->assertGreaterThan(0, (int)$id);
    }

    public function testCreateTransizioneWithDefaults(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        $data = ['media_id' => $mediaId];

        $id = $this->transizioneModel->create($data);
        
        $transizione = $this->transizioneModel->getByMedia($mediaId);
        $this->assertEquals('fade_to_black', $transizione['tipo_dissolvenza']);
        $this->assertEquals(0, $transizione['durata_sec']);
    }

    public function testGetByMedia(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        $this->insertTestData('transizioni', [
            'media_id' => $mediaId,
            'tipo_dissolvenza' => 'crossfade',
            'durata_sec' => 1.0
        ]);

        $transizione = $this->transizioneModel->getByMedia($mediaId);
        
        $this->assertIsArray($transizione);
        $this->assertEquals('crossfade', $transizione['tipo_dissolvenza']);
    }

    public function testGetByMediaNonExistent(): void
    {
        $transizione = $this->transizioneModel->getByMedia(99999);
        $this->assertFalse($transizione);
    }

    public function testUpdateTransizione(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        $this->insertTestData('transizioni', [
            'media_id' => $mediaId,
            'tipo_dissolvenza' => 'fade_to_black',
            'durata_sec' => 1.0
        ]);

        $updateData = [
            'tipo_dissolvenza' => 'cut',
            'durata_sec' => 0,
            'offset_prima_sec' => 0,
            'offset_dopo_sec' => 0
        ];

        $result = $this->transizioneModel->update($mediaId, $updateData);
        
        $this->assertTrue($result);
        
        $transizione = $this->transizioneModel->getByMedia($mediaId);
        $this->assertEquals('cut', $transizione['tipo_dissolvenza']);
    }

    public function testDeleteTransizione(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        $this->insertTestData('transizioni', [
            'media_id' => $mediaId,
            'tipo_dissolvenza' => 'fade_to_black',
            'durata_sec' => 1.0
        ]);

        $result = $this->transizioneModel->delete($mediaId);
        
        $this->assertTrue($result);
        
        $transizione = $this->transizioneModel->getByMedia($mediaId);
        $this->assertFalse($transizione);
    }

    public function testGetOrCreate(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        // First call should create
        $transizione = $this->transizioneModel->getOrCreate($mediaId);
        
        $this->assertIsArray($transizione);
        $this->assertEquals('fade_to_black', $transizione['tipo_dissolvenza']);
        
        // Second call should return existing
        $transizione2 = $this->transizioneModel->getOrCreate($mediaId);
        $this->assertEquals($transizione['id'], $transizione2['id']);
    }

    public function testGetOrCreateExisting(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        $this->insertTestData('transizioni', [
            'media_id' => $mediaId,
            'tipo_dissolvenza' => 'crossfade',
            'durata_sec' => 2.0
        ]);

        $transizione = $this->transizioneModel->getOrCreate($mediaId);
        
        $this->assertIsArray($transizione);
        $this->assertEquals('crossfade', $transizione['tipo_dissolvenza']);
        $this->assertEquals(2.0, $transizione['durata_sec']);
    }
}
