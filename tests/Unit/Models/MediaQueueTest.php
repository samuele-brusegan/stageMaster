<?php

namespace Tests\Unit\Models;

use App\Models\MediaQueue;
use Tests\TestCase;

class MediaQueueTest extends TestCase
{
    private MediaQueue $queueModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->queueModel = new MediaQueue($this->db);
        
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
                tipo_media ENUM('VIDEO', 'AUDIO', 'FOTO')
            )
        ");
        
        $this->executeSql("
            CREATE TABLE IF NOT EXISTS media_queue (
                id INT AUTO_INCREMENT PRIMARY KEY,
                talento_id INT,
                media_id INT,
                ordine_coda INT NOT NULL,
                stato ENUM('pending', 'playing', 'completed', 'skipped') DEFAULT 'pending'
            )
        ");
    }

    public function testAddToQueue(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4',
            'tipo_media' => 'VIDEO'
        ]);

        $data = [
            'talento_id' => $talentId,
            'media_id' => $mediaId,
            'stato' => 'pending'
        ];

        $id = $this->queueModel->add($data);
        
        $this->assertIsInt((int)$id);
        $this->assertGreaterThan(0, (int)$id);
        $this->assertEquals(1, $id); // First item should have order 1
    }

    public function testAddToQueueWithAutoIncrement(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        
        $mediaId1 = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test1.mp4',
            'tipo_media' => 'VIDEO'
        ]);
        
        $mediaId2 = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test2.mp4',
            'tipo_media' => 'VIDEO'
        ]);

        $this->queueModel->add(['talento_id' => $talentId, 'media_id' => $mediaId1]);
        $this->queueModel->add(['talento_id' => $talentId, 'media_id' => $mediaId2]);
        
        $queue = $this->queueModel->getAll();
        $this->assertEquals(1, $queue[0]['ordine_coda']);
        $this->assertEquals(2, $queue[1]['ordine_coda']);
    }

    public function testGetAll(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        
        $mediaId1 = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test1.mp4',
            'tipo_media' => 'VIDEO'
        ]);
        
        $mediaId2 = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'gobbo',
            'file_path' => '/media/test2.jpg',
            'tipo_media' => 'FOTO'
        ]);

        $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId1,
            'ordine_coda' => 1,
            'stato' => 'pending'
        ]);

        $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId2,
            'ordine_coda' => 2,
            'stato' => 'playing'
        ]);

        $queue = $this->queueModel->getAll();
        
        $this->assertIsArray($queue);
        $this->assertCount(2, $queue);
        $this->assertArrayHasKey('talento_nome', $queue[0]);
        $this->assertArrayHasKey('file_path', $queue[0]);
    }

    public function testGetByTalento(): void
    {
        $talentId1 = $this->insertTestData('talenti', ['nome' => 'Talent 1']);
        $talentId2 = $this->insertTestData('talenti', ['nome' => 'Talent 2']);
        
        $mediaId1 = $this->insertTestData('media_performance', [
            'talento_id' => $talentId1,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test1.mp4',
            'tipo_media' => 'VIDEO'
        ]);
        
        $mediaId2 = $this->insertTestData('media_performance', [
            'talento_id' => $talentId2,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test2.mp4',
            'tipo_media' => 'VIDEO'
        ]);

        $this->insertTestData('media_queue', [
            'talento_id' => $talentId1,
            'media_id' => $mediaId1,
            'ordine_coda' => 1,
            'stato' => 'pending'
        ]);

        $this->insertTestData('media_queue', [
            'talento_id' => $talentId2,
            'media_id' => $mediaId2,
            'ordine_coda' => 2,
            'stato' => 'pending'
        ]);

        $queue = $this->queueModel->getByTalento($talentId1);
        
        $this->assertIsArray($queue);
        $this->assertCount(1, $queue);
        $this->assertEquals($talentId1, $queue[0]['talento_id']);
    }

    public function testUpdateStatus(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4',
            'tipo_media' => 'VIDEO'
        ]);

        $queueId = $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId,
            'ordine_coda' => 1,
            'stato' => 'pending'
        ]);

        $result = $this->queueModel->updateStatus($queueId, 'playing');
        
        $this->assertTrue($result);
        
        $queue = $this->queueModel->getAll();
        $this->assertEquals('playing', $queue[0]['stato']);
    }

    public function testReorder(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        
        $mediaId1 = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test1.mp4',
            'tipo_media' => 'VIDEO'
        ]);
        
        $mediaId2 = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test2.mp4',
            'tipo_media' => 'VIDEO'
        ]);
        
        $mediaId3 = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test3.mp4',
            'tipo_media' => 'VIDEO'
        ]);

        $id1 = $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId1,
            'ordine_coda' => 1,
            'stato' => 'pending'
        ]);

        $id2 = $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId2,
            'ordine_coda' => 2,
            'stato' => 'pending'
        ]);

        $id3 = $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId3,
            'ordine_coda' => 3,
            'stato' => 'pending'
        ]);

        // Reverse order
        $result = $this->queueModel->reorder([$id3, $id2, $id1]);
        
        $this->assertTrue($result);
        
        $queue = $this->queueModel->getAll();
        $this->assertEquals($id3, $queue[0]['id']);
        $this->assertEquals(1, $queue[0]['ordine_coda']);
        $this->assertEquals($id2, $queue[1]['id']);
        $this->assertEquals(2, $queue[1]['ordine_coda']);
    }

    public function testRemove(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4',
            'tipo_media' => 'VIDEO'
        ]);

        $queueId = $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId,
            'ordine_coda' => 1,
            'stato' => 'pending'
        ]);

        $result = $this->queueModel->remove($queueId);
        
        $this->assertTrue($result);
        
        $queue = $this->queueModel->getAll();
        $this->assertEmpty($queue);
    }

    public function testGetPlaying(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        
        $mediaId1 = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test1.mp4',
            'tipo_media' => 'VIDEO'
        ]);
        
        $mediaId2 = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test2.mp4',
            'tipo_media' => 'VIDEO'
        ]);

        $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId1,
            'ordine_coda' => 1,
            'stato' => 'completed'
        ]);

        $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId2,
            'ordine_coda' => 2,
            'stato' => 'playing'
        ]);

        $playing = $this->queueModel->getPlaying();
        
        $this->assertIsArray($playing);
        $this->assertEquals('playing', $playing['stato']);
    }

    public function testGetPlayingNone(): void
    {
        $playing = $this->queueModel->getPlaying();
        $this->assertFalse($playing);
    }

    public function testGetNextPending(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        
        $mediaId1 = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test1.mp4',
            'tipo_media' => 'VIDEO'
        ]);
        
        $mediaId2 = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test2.mp4',
            'tipo_media' => 'VIDEO'
        ]);

        $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId1,
            'ordine_coda' => 1,
            'stato' => 'completed'
        ]);

        $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId2,
            'ordine_coda' => 2,
            'stato' => 'pending'
        ]);

        $next = $this->queueModel->getNextPending();
        
        $this->assertIsArray($next);
        $this->assertEquals('pending', $next['stato']);
    }

    public function testGetNextPendingNone(): void
    {
        $next = $this->queueModel->getNextPending();
        $this->assertFalse($next);
    }
}
