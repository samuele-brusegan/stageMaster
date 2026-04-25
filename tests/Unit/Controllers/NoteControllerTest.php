<?php

namespace Tests\Unit\Controllers;

use App\Models\NoteTecniche;
use Tests\TestCase;

class NoteControllerTest extends TestCase
{
    private NoteTecniche $noteModel;

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
            CREATE TABLE IF NOT EXISTS note_tecniche (
                id INT AUTO_INCREMENT PRIMARY KEY,
                talento_id INT NULL,
                tipo ENUM('materiale_palco', 'luci', 'generiche', 'pause') NOT NULL,
                contenuto TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )
        ");
        
        $this->noteModel = new NoteTecniche($this->db);
    }

    public function testIndexReturnsAllNotes(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);

        $this->insertTestData('note_tecniche', [
            'talento_id' => $talentId,
            'tipo' => 'materiale_palco',
            'contenuto' => 'Note 1'
        ]);

        $this->insertTestData('note_tecniche', [
            'talento_id' => $talentId,
            'tipo' => 'luci',
            'contenuto' => 'Note 2'
        ]);

        $result = $this->noteModel->getAll();
        
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }

    public function testShowReturnsNotesByTalento(): void
    {
        $talentId1 = $this->insertTestData('talenti', ['nome' => 'Talent 1']);
        $talentId2 = $this->insertTestData('talenti', ['nome' => 'Talent 2']);

        $this->insertTestData('note_tecniche', [
            'talento_id' => $talentId1,
            'tipo' => 'materiale_palco',
            'contenuto' => 'Note for Talent 1'
        ]);

        $this->insertTestData('note_tecniche', [
            'talento_id' => $talentId2,
            'tipo' => 'luci',
            'contenuto' => 'Note for Talent 2'
        ]);

        $result = $this->noteModel->getByTalento($talentId1);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals('Note for Talent 1', $result[0]['contenuto']);
    }

    public function testGroupedReturnsNotesByType(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);

        $this->insertTestData('note_tecniche', [
            'talento_id' => $talentId,
            'tipo' => 'materiale_palco',
            'contenuto' => 'Stage note'
        ]);

        $this->insertTestData('note_tecniche', [
            'talento_id' => $talentId,
            'tipo' => 'luci',
            'contenuto' => 'Light note'
        ]);

        $result = $this->noteModel->getGroupedByType($talentId);
        
        $this->assertIsArray($result);
        $this->assertArrayHasKey('materiale_palco', $result);
        $this->assertArrayHasKey('luci', $result);
        $this->assertCount(1, $result['materiale_palco']);
        $this->assertCount(1, $result['luci']);
    }

    public function testCreateNote(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);

        $result = $this->noteModel->create([
            'talento_id' => $talentId,
            'tipo' => 'materiale_palco',
            'contenuto' => 'New note'
        ]);
        
        $this->assertIsInt((int)$result);
        $this->assertGreaterThan(0, (int)$result);
    }

    public function testUpdateNote(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);

        $id = $this->insertTestData('note_tecniche', [
            'talento_id' => $talentId,
            'tipo' => 'materiale_palco',
            'contenuto' => 'Original'
        ]);

        $result = $this->noteModel->update($id, [
            'talento_id' => $talentId,
            'tipo' => 'luci',
            'contenuto' => 'Updated'
        ]);
        
        $this->assertTrue($result);
        
        $notes = $this->noteModel->getAll();
        $this->assertEquals('Updated', $notes[0]['contenuto']);
    }

    public function testDeleteNote(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);

        $id = $this->insertTestData('note_tecniche', [
            'talento_id' => $talentId,
            'tipo' => 'materiale_palco',
            'contenuto' => 'To delete'
        ]);

        $result = $this->noteModel->delete($id);
        
        $this->assertTrue($result);
        
        $notes = $this->noteModel->getAll();
        $this->assertEmpty($notes);
    }
}
