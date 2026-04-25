<?php

namespace Tests\Unit\Controllers;

use App\Models\PlayerState;
use Tests\TestCase;

class PlayerStateControllerTest extends TestCase
{
    private PlayerState $stateModel;

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
            CREATE TABLE IF NOT EXISTS player_state (
                component ENUM('proiettore', 'gobbo') PRIMARY KEY,
                current_talento_id INT,
                current_media_id INT,
                status ENUM('playing', 'paused', 'stopped') DEFAULT 'stopped',
                last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        $this->stateModel = new PlayerState($this->db);
    }

    public function testIndexReturnsAllStates(): void
    {
        $this->stateModel->updateState('proiettore', ['status' => 'playing']);
        $this->stateModel->updateState('gobbo', ['status' => 'stopped']);

        $result = $this->stateModel->getAllStates();
        
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testShowReturnsComponentState(): void
    {
        $this->stateModel->updateState('proiettore', ['status' => 'playing']);

        $result = $this->stateModel->getState('proiettore');
        
        $this->assertIsArray($result);
        $this->assertEquals('proiettore', $result['component']);
        $this->assertEquals('playing', $result['status']);
    }

    public function testShowNonExistentReturnsFalse(): void
    {
        $result = $this->stateModel->getState('nonexistent');
        $this->assertFalse($result);
    }

    public function testUpdateCreatesState(): void
    {
        $result = $this->stateModel->updateState('proiettore', [
            'status' => 'playing'
        ]);
        
        $this->assertTrue($result);
        
        $state = $this->stateModel->getState('proiettore');
        $this->assertEquals('playing', $state['status']);
    }

    public function testUpdateModifiesState(): void
    {
        $this->stateModel->updateState('proiettore', ['status' => 'playing']);
        
        $result = $this->stateModel->updateState('proiettore', ['status' => 'paused']);
        
        $this->assertTrue($result);
        
        $state = $this->stateModel->getState('proiettore');
        $this->assertEquals('paused', $state['status']);
    }

    public function testUpdateWithTalentoAndMedia(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        $result = $this->stateModel->updateState('proiettore', [
            'current_talento_id' => $talentId,
            'current_media_id' => $mediaId,
            'status' => 'playing'
        ]);
        
        $this->assertTrue($result);
        
        $state = $this->stateModel->getState('proiettore');
        $this->assertEquals($talentId, $state['current_talento_id']);
        $this->assertEquals($mediaId, $state['current_media_id']);
    }
}
