<?php

namespace Tests\Unit\Controllers;

use App\Models\Transizione;
use Tests\TestCase;

class TransizioneControllerTest extends TestCase
{
    private Transizione $transizioneModel;

    protected function setUp(): void
    {
        parent::setUp();
        
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
        
        $this->transizioneModel = new Transizione($this->db);
    }

    public function testShowReturnsTransizione(): void
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

        $result = $this->transizioneModel->getByMedia($mediaId);
        
        $this->assertIsArray($result);
        $this->assertEquals('crossfade', $result['tipo_dissolvenza']);
    }

    public function testShowNonExistentReturnsFalse(): void
    {
        $result = $this->transizioneModel->getByMedia(99999);
        $this->assertFalse($result);
    }

    public function testCreateTransizione(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        $result = $this->transizioneModel->create([
            'media_id' => $mediaId,
            'tipo_dissolvenza' => 'fade_to_black',
            'durata_sec' => 1.5
        ]);
        
        $this->assertIsInt((int)$result);
        $this->assertGreaterThan(0, (int)$result);
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

        $result = $this->transizioneModel->update($mediaId, [
            'tipo_dissolvenza' => 'cut',
            'durata_sec' => 0,
            'offset_prima_sec' => 0,
            'offset_dopo_sec' => 0
        ]);
        
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

    public function testGetOrCreateCreatesNew(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        $result = $this->transizioneModel->getOrCreate($mediaId);
        
        $this->assertIsArray($result);
        $this->assertEquals('fade_to_black', $result['tipo_dissolvenza']);
    }

    public function testGetOrCreateReturnsExisting(): void
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

        $result = $this->transizioneModel->getOrCreate($mediaId);
        
        $this->assertIsArray($result);
        $this->assertEquals('crossfade', $result['tipo_dissolvenza']);
    }
}
