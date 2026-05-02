<?php

namespace Tests\Unit\Models;

use App\Models\Screen;
use Tests\TestCase;

class ScreenTest extends TestCase
{
    private Screen $screenModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->screenModel = new Screen($this->db);
        
        $this->executeSql("
            CREATE TABLE IF NOT EXISTS screens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(50) NOT NULL,
                tipo ENUM('indipendente', 'mirror') DEFAULT 'indipendente',
                screen_riferimento_id INT NULL
            )
        ");
    }

    public function testCreateScreen(): void
    {
        $data = [
            'nome' => 'Main Screen',
            'tipo' => 'indipendente',
            'screen_riferimento_id' => null
        ];

        $id = $this->screenModel->create($data);
        
        $this->assertIsInt((int)$id);
        $this->assertGreaterThan(0, (int)$id);
        
        $screen = $this->screenModel->find($id);
        $this->assertEquals('Main Screen', $screen['nome']);
        $this->assertEquals('indipendente', $screen['tipo']);
    }

    public function testCreateScreenWithDefaults(): void
    {
        $data = [
            'nome' => 'Default Screen'
        ];

        $id = $this->screenModel->create($data);
        
        $screen = $this->screenModel->find($id);
        $this->assertEquals('indipendente', $screen['tipo']);
        $this->assertNull($screen['screen_riferimento_id']);
    }

    public function testCreateScreenTrimsName(): void
    {
        $id = $this->screenModel->create([
            'nome' => '  Trimmed Screen  ',
            'tipo' => 'indipendente'
        ]);

        $screen = $this->screenModel->find($id);
        $this->assertEquals('Trimmed Screen', $screen['nome']);
    }

    public function testCreateScreenRequiresName(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->screenModel->create(['nome' => '   ']);
    }

    public function testCreateScreenRejectsInvalidType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->screenModel->create([
            'nome' => 'Invalid Type',
            'tipo' => 'external'
        ]);
    }

    public function testCreateMirrorScreenRejectsInvalidReference(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->screenModel->create([
            'nome' => 'Broken Mirror',
            'tipo' => 'mirror',
            'screen_riferimento_id' => 99999
        ]);
    }

    public function testCreateMirrorScreen(): void
    {
        $refId = $this->insertTestData('screens', [
            'nome' => 'Reference Screen',
            'tipo' => 'indipendente'
        ]);

        $data = [
            'nome' => 'Mirror Screen',
            'tipo' => 'mirror',
            'screen_riferimento_id' => $refId
        ];

        $id = $this->screenModel->create($data);
        
        $screen = $this->screenModel->find($id);
        $this->assertEquals('mirror', $screen['tipo']);
        $this->assertEquals($refId, $screen['screen_riferimento_id']);
    }

    public function testFindScreen(): void
    {
        $id = $this->insertTestData('screens', [
            'nome' => 'Find Test',
            'tipo' => 'indipendente'
        ]);

        $screen = $this->screenModel->find($id);
        
        $this->assertIsArray($screen);
        $this->assertEquals($id, $screen['id']);
        $this->assertEquals('Find Test', $screen['nome']);
    }

    public function testFindNonExistentScreen(): void
    {
        $screen = $this->screenModel->find(99999);
        $this->assertFalse($screen);
    }

    public function testUpdateScreen(): void
    {
        $id = $this->insertTestData('screens', [
            'nome' => 'Original Name',
            'tipo' => 'indipendente'
        ]);

        $updateData = [
            'nome' => 'Updated Name',
            'tipo' => 'mirror',
            'screen_riferimento_id' => null
        ];

        $result = $this->screenModel->update($id, $updateData);
        
        $this->assertTrue($result);
        
        $screen = $this->screenModel->find($id);
        $this->assertEquals('Updated Name', $screen['nome']);
        $this->assertEquals('mirror', $screen['tipo']);
    }

    public function testDeleteScreen(): void
    {
        $id = $this->insertTestData('screens', [
            'nome' => 'To Delete',
            'tipo' => 'indipendente'
        ]);

        $result = $this->screenModel->delete($id);
        
        $this->assertTrue($result);
        
        $screen = $this->screenModel->find($id);
        $this->assertFalse($screen);
    }

    public function testGetAll(): void
    {
        $this->insertTestData('screens', ['nome' => 'Screen 1', 'tipo' => 'indipendente']);
        $this->insertTestData('screens', ['nome' => 'Screen 2', 'tipo' => 'mirror']);
        $this->insertTestData('screens', ['nome' => 'Screen 3', 'tipo' => 'indipendente']);

        $screens = $this->screenModel->getAll();
        
        $this->assertIsArray($screens);
        $this->assertCount(3, $screens);
        $this->assertArrayHasKey('screen_riferimento_nome', $screens[0]);
    }

    public function testGetAllEmpty(): void
    {
        $screens = $this->screenModel->getAll();
        
        $this->assertIsArray($screens);
        $this->assertEmpty($screens);
    }

    public function testGetMedia(): void
    {
        // Create media_performance table
        $this->executeSql("
            CREATE TABLE IF NOT EXISTS media_performance (
                id INT AUTO_INCREMENT PRIMARY KEY,
                talento_id INT,
                tipo_output ENUM('proiettore', 'gobbo') NOT NULL,
                file_path VARCHAR(255) NOT NULL,
                friendly_name VARCHAR(100) NULL,
                screen_id INT,
                tipo_media ENUM('VIDEO', 'AUDIO', 'FOTO') DEFAULT 'VIDEO',
                timestamp_inizio TIME DEFAULT '00:00:00',
                timestamp_fine TIME NULL,
                durata_totale_sec INT NULL,
                fade_in_sec INT DEFAULT 0,
                fade_out_sec INT DEFAULT 0,
                ordine_esecuzione INT
            )
        ");

        $this->executeSql("
            CREATE TABLE IF NOT EXISTS talenti (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL
            )
        ");

        $screenId = $this->insertTestData('screens', [
            'nome' => 'Test Screen',
            'tipo' => 'indipendente'
        ]);

        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);

        $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4',
            'screen_id' => $screenId,
            'ordine_esecuzione' => 1
        ]);

        $media = $this->screenModel->getMedia($screenId);
        
        $this->assertIsArray($media);
        $this->assertCount(1, $media);
        $this->assertEquals('/media/test.mp4', $media[0]['file_path']);
    }

    public function testGetMediaEmpty(): void
    {
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
                file_path VARCHAR(255) NOT NULL,
                friendly_name VARCHAR(100) NULL,
                screen_id INT,
                tipo_media ENUM('VIDEO', 'AUDIO', 'FOTO') DEFAULT 'VIDEO',
                timestamp_inizio TIME DEFAULT '00:00:00',
                timestamp_fine TIME NULL,
                durata_totale_sec INT NULL,
                fade_in_sec INT DEFAULT 0,
                fade_out_sec INT DEFAULT 0,
                ordine_esecuzione INT
            )
        ");

        $screenId = $this->insertTestData('screens', [
            'nome' => 'Empty Screen',
            'tipo' => 'indipendente'
        ]);

        $media = $this->screenModel->getMedia($screenId);
        
        $this->assertIsArray($media);
        $this->assertEmpty($media);
    }
}
