<?php

namespace Tests\Unit\Controllers;

use App\Models\MediaQueue;
use Tests\TestCase;

class QueueControllerTest extends TestCase
{
    private MediaQueue $queueModel;

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
        
        $this->queueModel = new MediaQueue($this->db);
    }

    public function testIndexReturnsAllQueue(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4',
            'tipo_media' => 'VIDEO'
        ]);

        $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId,
            'ordine_coda' => 1,
            'stato' => 'pending'
        ]);

        $result = $this->queueModel->getAll();
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
    }

    public function testShowReturnsQueueByTalento(): void
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

        $result = $this->queueModel->getByTalento($talentId1);
        
        $this->assertIsArray($result);
        $this->assertCount(1, $result);
        $this->assertEquals($talentId1, $result[0]['talento_id']);
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

        $result = $this->queueModel->add([
            'talento_id' => $talentId,
            'media_id' => $mediaId,
            'stato' => 'pending'
        ]);
        
        $this->assertIsInt((int)$result);
        $this->assertGreaterThan(0, (int)$result);
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

    public function testReorderQueue(): void
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

        $result = $this->queueModel->reorder([$id2, $id1]);
        
        $this->assertTrue($result);
        
        $queue = $this->queueModel->getAll();
        $this->assertEquals($id2, $queue[0]['id']);
        $this->assertEquals(1, $queue[0]['ordine_coda']);
    }

    public function testRemoveFromQueue(): void
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

    public function testPlayingReturnsCurrent(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4',
            'tipo_media' => 'VIDEO'
        ]);

        $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId,
            'ordine_coda' => 1,
            'stato' => 'playing'
        ]);

        $result = $this->queueModel->getPlaying();
        
        $this->assertIsArray($result);
        $this->assertEquals('playing', $result['stato']);
    }

    public function testNextReturnsPending(): void
    {
        $talentId = $this->insertTestData('talenti', ['nome' => 'Test Talent']);
        $mediaId = $this->insertTestData('media_performance', [
            'talento_id' => $talentId,
            'tipo_output' => 'proiettore',
            'file_path' => '/media/test.mp4',
            'tipo_media' => 'VIDEO'
        ]);

        $this->insertTestData('media_queue', [
            'talento_id' => $talentId,
            'media_id' => $mediaId,
            'ordine_coda' => 1,
            'stato' => 'pending'
        ]);

        $result = $this->queueModel->getNextPending();
        
        $this->assertIsArray($result);
        $this->assertEquals('pending', $result['stato']);
    }
}
