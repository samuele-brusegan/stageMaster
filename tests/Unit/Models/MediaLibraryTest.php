<?php

namespace Tests\Unit\Models;

use App\Models\MediaLibrary;
use Tests\TestCase;

class MediaLibraryTest extends TestCase
{
    private MediaLibrary $mediaLibraryModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mediaLibraryModel = new MediaLibrary($this->db);
        
        $this->executeSql("
            CREATE TABLE IF NOT EXISTS media (
                id INT AUTO_INCREMENT PRIMARY KEY,
                file_name VARCHAR(255) NOT NULL,
                file_path VARCHAR(255) NOT NULL,
                file_type ENUM('VIDEO', 'AUDIO', 'FOTO') NOT NULL,
                file_size INT,
                duration_sec INT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ");
    }

    public function testCreateMedia(): void
    {
        $data = [
            'file_name' => 'test.mp4',
            'file_path' => '/media/test.mp4',
            'file_type' => 'VIDEO',
            'file_size' => 1024000,
            'duration_sec' => 180
        ];

        $id = $this->mediaLibraryModel->create($data);
        
        $this->assertIsInt((int)$id);
        $this->assertGreaterThan(0, (int)$id);
        
        $media = $this->mediaLibraryModel->find($id);
        $this->assertEquals('test.mp4', $media['file_name']);
        $this->assertEquals('VIDEO', $media['file_type']);
    }

    public function testCreateMediaWithDefaults(): void
    {
        $data = [
            'file_name' => 'test.jpg',
            'file_path' => '/media/test.jpg',
            'file_type' => 'FOTO'
        ];

        $id = $this->mediaLibraryModel->create($data);
        
        $media = $this->mediaLibraryModel->find($id);
        $this->assertNull($media['file_size']);
        $this->assertNull($media['duration_sec']);
    }

    public function testFindMedia(): void
    {
        $id = $this->insertTestData('media', [
            'file_name' => 'find.mp4',
            'file_path' => '/media/find.mp4',
            'file_type' => 'VIDEO'
        ]);

        $media = $this->mediaLibraryModel->find($id);
        
        $this->assertIsArray($media);
        $this->assertEquals($id, $media['id']);
        $this->assertEquals('find.mp4', $media['file_name']);
    }

    public function testFindNonExistentMedia(): void
    {
        $media = $this->mediaLibraryModel->find(99999);
        $this->assertFalse($media);
    }

    public function testFindByPath(): void
    {
        $this->insertTestData('media', [
            'file_name' => 'path.mp4',
            'file_path' => '/media/path.mp4',
            'file_type' => 'VIDEO'
        ]);

        $media = $this->mediaLibraryModel->findByPath('/media/path.mp4');
        
        $this->assertIsArray($media);
        $this->assertEquals('/media/path.mp4', $media['file_path']);
    }

    public function testFindByPathNonExistent(): void
    {
        $media = $this->mediaLibraryModel->findByPath('/media/nonexistent.mp4');
        $this->assertFalse($media);
    }

    public function testGetAll(): void
    {
        $this->insertTestData('media', [
            'file_name' => 'video1.mp4',
            'file_path' => '/media/video1.mp4',
            'file_type' => 'VIDEO'
        ]);

        $this->insertTestData('media', [
            'file_name' => 'audio1.mp3',
            'file_path' => '/media/audio1.mp3',
            'file_type' => 'AUDIO'
        ]);

        $mediaList = $this->mediaLibraryModel->getAll();
        
        $this->assertIsArray($mediaList);
        $this->assertCount(2, $mediaList);
    }

    public function testGetAllEmpty(): void
    {
        $mediaList = $this->mediaLibraryModel->getAll();
        
        $this->assertIsArray($mediaList);
        $this->assertEmpty($mediaList);
    }

    public function testGetByType(): void
    {
        $this->insertTestData('media', [
            'file_name' => 'video1.mp4',
            'file_path' => '/media/video1.mp4',
            'file_type' => 'VIDEO'
        ]);

        $this->insertTestData('media', [
            'file_name' => 'video2.mp4',
            'file_path' => '/media/video2.mp4',
            'file_type' => 'VIDEO'
        ]);

        $this->insertTestData('media', [
            'file_name' => 'audio1.mp3',
            'file_path' => '/media/audio1.mp3',
            'file_type' => 'AUDIO'
        ]);

        $videos = $this->mediaLibraryModel->getByType('VIDEO');
        
        $this->assertIsArray($videos);
        $this->assertCount(2, $videos);
        $this->assertEquals('VIDEO', $videos[0]['file_type']);
    }

    public function testDeleteMedia(): void
    {
        $id = $this->insertTestData('media', [
            'file_name' => 'delete.mp4',
            'file_path' => '/media/delete.mp4',
            'file_type' => 'VIDEO'
        ]);

        $result = $this->mediaLibraryModel->delete($id);
        
        $this->assertTrue($result);
        
        $media = $this->mediaLibraryModel->find($id);
        $this->assertFalse($media);
    }
}
