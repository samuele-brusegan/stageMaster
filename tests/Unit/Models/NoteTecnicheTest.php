<?php

namespace Tests\Unit\Models;

use App\Models\NoteTecniche;
use Tests\TestCase;

class NoteTecnicheTest extends TestCase
{
    private NoteTecniche $noteModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->noteModel = new NoteTecniche($this->db);
        
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
    }

    public function testCreateNote(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);

        $data = [
            'talento_id' => $talentId,
            'tipo' => 'materiale_palco',
            'contenuto' => 'Microfono al centro'
        ];

        $id = $this->noteModel->create($data);
        
        $this->assertIsInt((int)$id);
        $this->assertGreaterThan(0, (int)$id);
    }

    public function testCreateNoteWithoutTalent(): void
    {
        $data = [
            'talento_id' => null,
            'tipo' => 'generiche',
            'contenuto' => 'Nota generale'
        ];

        $id = $this->noteModel->create($data);
        
        $this->assertIsInt((int)$id);
        $this->assertGreaterThan(0, (int)$id);
    }

    public function testGetAll(): void
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

        $notes = $this->noteModel->getAll();
        
        $this->assertIsArray($notes);
        $this->assertCount(2, $notes);
        $this->assertArrayHasKey('talento_nome', $notes[0]);
    }

    public function testGetByTalento(): void
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

        $notes = $this->noteModel->getByTalento($talentId1);
        
        $this->assertIsArray($notes);
        $this->assertCount(1, $notes);
        $this->assertEquals('Note for Talent 1', $notes[0]['contenuto']);
    }

    public function testGetByType(): void
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

        $this->insertTestData('note_tecniche', [
            'talento_id' => $talentId,
            'tipo' => 'materiale_palco',
            'contenuto' => 'Another stage note'
        ]);

        $notes = $this->noteModel->getByType('materiale_palco');
        
        $this->assertIsArray($notes);
        $this->assertCount(2, $notes);
    }

    public function testUpdateNote(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);

        $id = $this->insertTestData('note_tecniche', [
            'talento_id' => $talentId,
            'tipo' => 'materiale_palco',
            'contenuto' => 'Original content'
        ]);

        $updateData = [
            'talento_id' => $talentId,
            'tipo' => 'luci',
            'contenuto' => 'Updated content'
        ];

        $result = $this->noteModel->update($id, $updateData);
        
        $this->assertTrue($result);
        
        $notes = $this->noteModel->getAll();
        $this->assertEquals('Updated content', $notes[0]['contenuto']);
        $this->assertEquals('luci', $notes[0]['tipo']);
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

    public function testGetGroupedByType(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);

        $this->insertTestData('note_tecniche', [
            'talento_id' => $talentId,
            'tipo' => 'materiale_palco',
            'contenuto' => 'Stage note 1'
        ]);

        $this->insertTestData('note_tecniche', [
            'talento_id' => $talentId,
            'tipo' => 'luci',
            'contenuto' => 'Light note'
        ]);

        $this->insertTestData('note_tecniche', [
            'talento_id' => $talentId,
            'tipo' => 'materiale_palco',
            'contenuto' => 'Stage note 2'
        ]);

        $grouped = $this->noteModel->getGroupedByType($talentId);
        
        $this->assertIsArray($grouped);
        $this->assertArrayHasKey('materiale_palco', $grouped);
        $this->assertArrayHasKey('luci', $grouped);
        $this->assertArrayHasKey('generiche', $grouped);
        $this->assertArrayHasKey('pause', $grouped);
        
        $this->assertCount(2, $grouped['materiale_palco']);
        $this->assertCount(1, $grouped['luci']);
        $this->assertCount(0, $grouped['generiche']);
    }

    public function testGetGroupedByTypeEmpty(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);

        $grouped = $this->noteModel->getGroupedByType($talentId);
        
        $this->assertIsArray($grouped);
        $this->assertCount(4, $grouped);
        $this->assertEmpty($grouped['materiale_palco']);
        $this->assertEmpty($grouped['luci']);
    }
}
