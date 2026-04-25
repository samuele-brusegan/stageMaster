<?php

namespace Tests\Unit\Models;

use App\Models\PlayerState;
use Tests\TestCase;

class PlayerStateTest extends TestCase
{
    private PlayerState $stateModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateModel = new PlayerState($this->db);
        
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
    }

    public function testUpdateStateInsert(): void
    {
        $data = [
            'current_talento_id' => null,
            'current_media_id' => null,
            'status' => 'stopped'
        ];

        $result = $this->stateModel->updateState('proiettore', $data);
        
        $this->assertTrue($result);
        
        $state = $this->stateModel->getState('proiettore');
        $this->assertEquals('stopped', $state['status']);
    }

    public function testUpdateStateUpsert(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        $data1 = [
            'current_talento_id' => $talentId,
            'current_media_id' => $mediaId,
            'status' => 'playing'
        ];

        $this->stateModel->updateState('proiettore', $data1);
        
        $data2 = [
            'current_talento_id' => $talentId,
            'current_media_id' => $mediaId,
            'status' => 'paused'
        ];

        $result = $this->stateModel->updateState('proiettore', $data2);
        
        $this->assertTrue($result);
        
        $state = $this->stateModel->getState('proiettore');
        $this->assertEquals('paused', $state['status']);
    }

    public function testUpdateStateWithDefaults(): void
    {
        $result = $this->stateModel->updateState('gobbo', []);
        
        $this->assertTrue($result);
        
        $state = $this->stateModel->getState('gobbo');
        $this->assertEquals('stopped', $state['status']);
        $this->assertNull($state['current_talento_id']);
        $this->assertNull($state['current_media_id']);
    }

    public function testGetState(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        $this->stateModel->updateState('proiettore', [
            'current_talento_id' => $talentId,
            'current_media_id' => $mediaId,
            'status' => 'playing'
        ]);

        $state = $this->stateModel->getState('proiettore');
        
        $this->assertIsArray($state);
        $this->assertEquals('proiettore', $state['component']);
        $this->assertEquals($talentId, $state['current_talento_id']);
        $this->assertEquals($mediaId, $state['current_media_id']);
        $this->assertEquals('playing', $state['status']);
    }

    public function testGetStateNonExistent(): void
    {
        $state = $this->stateModel->getState('proiettore');
        $this->assertFalse($state);
    }

    public function testGetAllStates(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4'
        ]);

        $this->stateModel->updateState('proiettore', [
            'current_talento_id' => $talentId,
            'current_media_id' => $mediaId,
            'status' => 'playing'
        ]);

        $this->stateModel->updateState('gobbo', [
            'current_talento_id' => null,
            'current_media_id' => null,
            'status' => 'stopped'
        ]);

        $states = $this->stateModel->getAllStates();
        
        $this->assertIsArray($states);
        $this->assertCount(2, $states);
    }

    public function testGetAllStatesEmpty(): void
    {
        $states = $this->stateModel->getAllStates();
        
        $this->assertIsArray($states);
        $this->assertEmpty($states);
    }

    public function testMultipleComponents(): void
    {
        $this->stateModel->updateState('proiettore', ['status' => 'playing']);
        $this->stateModel->updateState('gobbo', ['status' => 'stopped']);

        $proiettoreState = $this->stateModel->getState('proiettore');
        $gobboState = $this->stateModel->getState('gobbo');
        
        $this->assertEquals('playing', $proiettoreState['status']);
        $this->assertEquals('stopped', $gobboState['status']);
        $this->assertEquals('proiettore', $proiettoreState['component']);
        $this->assertEquals('gobbo', $gobboState['component']);
    }
}
