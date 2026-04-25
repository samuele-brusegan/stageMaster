<?php

namespace Tests\Unit\Controllers;

use App\Models\Screen;
use Tests\TestCase;

class ScreenControllerTest extends TestCase
{
    private Screen $screenModel;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->executeSql("
            CREATE TABLE IF NOT EXISTS screens (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(50) NOT NULL,
                tipo ENUM('indipendente', 'mirror') DEFAULT 'indipendente',
                screen_riferimento_id INT NULL
            )
        ");
        
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
                screen_id INT,
                ordine_esecuzione INT
            )
        ");
        
        $this->screenModel = new Screen($this->db);
    }

    public function testIndexReturnsAllScreens(): void
    {
        $this->insertTestData('screens', ['nome' => 'Screen 1', 'tipo' => 'indipendente']);
        $this->insertTestData('screens', ['nome' => 'Screen 2', 'tipo' => 'mirror']);

        $result = $this->screenModel->getAll();
        
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testShowReturnsScreen(): void
    {
        $id = $this->insertTestData('screens', [
            'nome' => 'Test Screen',
            'tipo' => 'indipendente'
        ]);

        $result = $this->screenModel->find($id);
        
        $this->assertIsArray($result);
        $this->assertEquals($id, $result['id']);
        $this->assertEquals('Test Screen', $result['nome']);
    }

    public function testShowNonExistentReturnsFalse(): void
    {
        $result = $this->screenModel->find(99999);
        $this->assertFalse($result);
    }

    public function testCreateScreen(): void
    {
        $result = $this->screenModel->create([
            'nome' => 'New Screen',
            'tipo' => 'indipendente'
        ]);
        
        $this->assertIsInt((int)$result);
        $this->assertGreaterThan(0, (int)$result);
    }

    public function testUpdateScreen(): void
    {
        $id = $this->insertTestData('screens', [
            'nome' => 'Original',
            'tipo' => 'indipendente'
        ]);

        $result = $this->screenModel->update($id, [
            'nome' => 'Updated',
            'tipo' => 'mirror',
            'screen_riferimento_id' => null
        ]);
        
        $this->assertTrue($result);
        
        $screen = $this->screenModel->find($id);
        $this->assertEquals('Updated', $screen['nome']);
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

    public function testGetMediaForScreen(): void
    {
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

        $result = $this->screenModel->getMedia($screenId);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }
}
