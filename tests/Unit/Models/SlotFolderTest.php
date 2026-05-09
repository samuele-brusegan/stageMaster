<?php

declare(strict_types=1);

namespace Tests\Unit\Models;

use App\Models\SlotFolder;
use RuntimeException;
use Tests\TestCase;

class SlotFolderTest extends TestCase
{
    private SlotFolder $folders;

    protected function setUp(): void
    {
        parent::setUp();

        $this->executeSql('CREATE TABLE IF NOT EXISTS slot_folders (
            id INT AUTO_INCREMENT PRIMARY KEY,
            parent_id INT NULL,
            nome VARCHAR(120) NOT NULL,
            ordine INT NOT NULL DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (parent_id) REFERENCES slot_folders(id) ON DELETE CASCADE
        )');

        $this->executeSql('CREATE TABLE IF NOT EXISTS talenti (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nome VARCHAR(100) NOT NULL,
            categoria VARCHAR(50),
            ordine_scaletta INT,
            folder_id INT NULL,
            FOREIGN KEY (folder_id) REFERENCES slot_folders(id) ON DELETE SET NULL
        )');

        $this->folders = new SlotFolder($this->db);
    }

    public function testCreateAssignsIncrementingOrdine(): void
    {
        $first  = $this->folders->create(['nome' => 'Atto 1']);
        $second = $this->folders->create(['nome' => 'Atto 2']);

        $a = $this->folders->find($first);
        $b = $this->folders->find($second);

        $this->assertSame(1, (int) $a['ordine']);
        $this->assertSame(2, (int) $b['ordine']);
        $this->assertNull($a['parent_id']);
    }

    public function testCreateRequiresName(): void
    {
        $this->expectException(RuntimeException::class);
        $this->folders->create(['nome' => '   ']);
    }

    public function testNestedChildrenAndCycleProtection(): void
    {
        $root  = $this->folders->create(['nome' => 'Atto 1']);
        $child = $this->folders->create(['nome' => 'Scena 1.1', 'parent_id' => $root]);

        $childRow = $this->folders->find($child);
        $this->assertSame($root, (int) $childRow['parent_id']);

        $this->expectException(RuntimeException::class);
        $this->folders->update($root, ['parent_id' => $child]);
    }

    public function testUpdateNameAndParent(): void
    {
        $a = $this->folders->create(['nome' => 'A']);
        $b = $this->folders->create(['nome' => 'B']);

        $this->folders->update($b, ['nome' => 'B prime', 'parent_id' => $a]);
        $row = $this->folders->find($b);
        $this->assertSame('B prime', $row['nome']);
        $this->assertSame($a, (int) $row['parent_id']);
    }

    public function testReorderUpdatesOrdineAndParent(): void
    {
        $a = $this->folders->create(['nome' => 'A']);
        $b = $this->folders->create(['nome' => 'B']);
        $c = $this->folders->create(['nome' => 'C']);

        $this->folders->reorder(null, [$c, $a, $b]);

        $rows = $this->folders->getAll();
        $byId = [];
        foreach ($rows as $row) {
            $byId[(int) $row['id']] = (int) $row['ordine'];
        }
        $this->assertSame(1, $byId[$c]);
        $this->assertSame(2, $byId[$a]);
        $this->assertSame(3, $byId[$b]);
    }

    public function testTreeWithSlotsGroupsUnfiledAndOwned(): void
    {
        $folder = $this->folders->create(['nome' => 'Atto 1']);

        $this->insertTestData('talenti', ['nome' => 'Slot A', 'ordine_scaletta' => 1, 'folder_id' => $folder]);
        $this->insertTestData('talenti', ['nome' => 'Slot B', 'ordine_scaletta' => 2, 'folder_id' => null]);

        $tree = $this->folders->getTreeWithSlots();

        $this->assertCount(1, $tree['folders']);
        $this->assertSame('Slot A', $tree['folders'][0]['slots'][0]['nome']);
        $this->assertCount(1, $tree['unfiled']);
        $this->assertSame('Slot B', $tree['unfiled'][0]['nome']);
    }

    public function testDeleteCascadesChildrenAndNullsSlots(): void
    {
        $folder = $this->folders->create(['nome' => 'Atto 1']);
        $child  = $this->folders->create(['nome' => 'Scena 1', 'parent_id' => $folder]);
        $slotId = $this->insertTestData('talenti', ['nome' => 'Slot', 'ordine_scaletta' => 1, 'folder_id' => $folder]);

        $this->folders->delete($folder);

        $this->assertNull($this->folders->find($folder));
        $this->assertNull($this->folders->find($child));

        $stmt = $this->db->prepare('SELECT folder_id FROM talenti WHERE id = ?');
        $stmt->execute([$slotId]);
        $this->assertNull($stmt->fetchColumn());
    }
}
