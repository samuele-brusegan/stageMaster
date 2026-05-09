<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Connection;
use App\Models\MediaLibrary;
use App\Support\MediaProbe;
use PDO;

class MediaLibraryController extends ApiController
{
    private PDO $db;
    private MediaLibrary $mediaLibrary;

    public function __construct()
    {
        $this->db = Connection::getInstance();
        $this->mediaLibrary = new MediaLibrary($this->db);
    }

    /**
     * Get all media
     */
    public function index(): void {
        try {
            $media = $this->mediaLibrary->getAll();
            $this->json(['status' => 'ok', 'data' => $media]);
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Upload one or more media files
     */
    public function upload(): void {
        try {
            $maxUploadBytes = 200 * 1024 * 1024;
            $contentLength = (int)($_SERVER['CONTENT_LENGTH'] ?? 0);
            if ($contentLength > $maxUploadBytes) {
                $this->json([
                    'status' => 'error',
                    'message' => 'File troppo grande. Limite massimo: 200 MB'
                ], 413);
            }

            if (!isset($_FILES['file'])) {
                $this->json(['status' => 'error', 'message' => 'Nessun file selezionato'], 400);
            }

            $files = $_FILES['file'];
            
            // Handle single file upload
            if (!is_array($files['name'])) {
                if ($files['error'] !== UPLOAD_ERR_OK) {
                    $message = $this->uploadErrorMessage($files['error']);
                    $this->json(['status' => 'error', 'message' => $message], 400);
                }
                
                $result = $this->processSingleFile($files, $maxUploadBytes);
                $this->json(['status' => 'ok', 'data' => $result]);
                return;
            }

            // Handle multiple files upload
            $uploadedFiles = [];
            $failedFiles = [];

            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                    $failedFiles[] = [
                        'file' => $files['name'][$i],
                        'error' => $this->uploadErrorMessage($files['error'][$i])
                    ];
                    continue;
                }

                $fileData = [
                    'name' => $files['name'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'size' => $files['size'][$i],
                    'error' => $files['error'][$i]
                ];

                try {
                    $result = $this->processSingleFile($fileData, $maxUploadBytes);
                    $uploadedFiles[] = $result;
                } catch (\Exception $e) {
                    $failedFiles[] = [
                        'file' => $files['name'][$i],
                        'error' => $e->getMessage()
                    ];
                }
            }

            if (empty($uploadedFiles) && !empty($failedFiles)) {
                $this->json([
                    'status' => 'error',
                    'message' => 'Nessun file è stato caricato con successo',
                    'failed_files' => $failedFiles
                ], 400);
            }

            $response = ['status' => 'ok', 'data' => $uploadedFiles];
            if (!empty($failedFiles)) {
                $response['failed_files'] = $failedFiles;
            }
            
            $this->json($response);
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function processSingleFile($file, $maxUploadBytes) {
        $fileName = $file['name'];
        $fileTmp = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if ($fileSize > $maxUploadBytes) {
            throw new \Exception('File troppo grande. Limite massimo: 200 MB');
        }

        // Determine file type
        $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $videoExts = ['mp4', 'webm', 'ogg', 'mov'];
        $audioExts = ['mp3', 'wav', 'ogg', 'flac'];

        if (in_array($fileExt, $imageExts)) {
            $fileType = 'FOTO';
        } elseif (in_array($fileExt, $videoExts)) {
            $fileType = 'VIDEO';
        } elseif (in_array($fileExt, $audioExts)) {
            $fileType = 'AUDIO';
        } else {
            throw new \Exception('Unsupported file type');
        }

        // Generate unique filename
        $baseName = pathinfo($fileName, PATHINFO_FILENAME);
        $uniqueName = $baseName . '_' . time() . '_' . uniqid() . '.' . $fileExt;
        $uploadPath = __DIR__ . '/../../public/media/' . $uniqueName;
        $webPath = '/media/' . $uniqueName;

        // Move file
        if (!move_uploaded_file($fileTmp, $uploadPath)) {
            throw new \Exception('Failed to move uploaded file');
        }

        // Get duration for video/audio (requires ffprobe)
        $durationSec = null;
        if ($fileType === 'VIDEO' || $fileType === 'AUDIO') {
            $durationSec = MediaProbe::durationFromAbsolutePath($uploadPath);
        }

        // Save to database
        $mediaId = $this->mediaLibrary->create([
            'file_name' => $fileName,
            'file_path' => $webPath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'duration_sec' => $durationSec
        ]);

        return [
            'id' => $mediaId,
            'file_name' => $fileName,
            'file_path' => $webPath,
            'file_type' => $fileType,
            'file_size' => $fileSize,
            'duration_sec' => $durationSec
        ];
    }

    private function uploadErrorMessage($errorCode) {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE => 'File troppo grande per la configurazione del server',
            UPLOAD_ERR_PARTIAL => 'Upload incompleto, riprova',
            UPLOAD_ERR_NO_FILE => 'Nessun file selezionato',
            UPLOAD_ERR_NO_TMP_DIR => 'Cartella temporanea upload mancante',
            UPLOAD_ERR_CANT_WRITE => 'Impossibile scrivere il file caricato',
            UPLOAD_ERR_EXTENSION => 'Upload bloccato da una estensione PHP',
            default => 'Errore upload sconosciuto'
        };
    }

    private function publicDir(): string
    {
        return realpath(__DIR__ . '/../../public') ?: __DIR__ . '/../../public';
    }

    /**
     * Recompute and persist duration for an existing library entry.
     * Useful for legacy rows registered before ffprobe was available.
     */
    public function refreshDuration(): void
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->json(['status' => 'error', 'message' => 'Missing media ID'], 400);
            return;
        }
        $media = $this->mediaLibrary->find((int) $id);
        if (!$media) {
            $this->json(['status' => 'error', 'message' => 'Media not found'], 404);
            return;
        }
        if (!in_array(strtoupper((string)$media['file_type']), ['AUDIO', 'VIDEO'], true)) {
            $this->json(['status' => 'ok', 'duration_sec' => null, 'message' => 'Tipo media senza durata']);
            return;
        }
        $duration = MediaProbe::durationFromWebPath((string)$media['file_path'], $this->publicDir());
        $this->mediaLibrary->updateDuration((int) $media['id'], $duration);
        $this->json([
            'status' => 'ok',
            'id' => (int) $media['id'],
            'duration_sec' => $duration,
            'message' => $duration === null ? 'Durata non rilevabile' : 'Durata aggiornata',
        ]);
    }

    /**
     * Delete media
     */
    public function delete(): void {
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) {
                $this->json(['status' => 'error', 'message' => 'Missing media ID'], 400);
            }

            $media = $this->mediaLibrary->find($id);
            if (!$media) {
                $this->json(['status' => 'error', 'message' => 'Media not found'], 404);
            }

            // Delete file
            $filePath = __DIR__ . '/../../public' . $media['file_path'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            // Delete from database
            $this->mediaLibrary->delete($id);

            $this->json(['status' => 'ok', 'message' => 'Media deleted']);
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Scan media directory for unregistered files
     */
    public function scan(): void {
        try {
            $mediaDir = __DIR__ . '/../../public/media';
            $files = scandir($mediaDir);
            $unregistered = [];

            $imageExts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $videoExts = ['mp4', 'webm', 'ogg', 'mov'];
            $audioExts = ['mp3', 'wav', 'ogg', 'flac'];

            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || $file === '.gitkeep') {
                    continue;
                }

                $filePath = '/media/' . $file;
                $existing = $this->mediaLibrary->findByPath($filePath);

                if (!$existing) {
                    $fileExt = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    $fullPath = $mediaDir . '/' . $file;
                    $fileSize = file_exists($fullPath) ? filesize($fullPath) : 0;

                    if (in_array($fileExt, $imageExts)) {
                        $fileType = 'FOTO';
                    } elseif (in_array($fileExt, $videoExts)) {
                        $fileType = 'VIDEO';
                    } elseif (in_array($fileExt, $audioExts)) {
                        $fileType = 'AUDIO';
                    } else {
                        continue;
                    }

                    $durationSec = null;
                    if ($fileType === 'AUDIO' || $fileType === 'VIDEO') {
                        $durationSec = MediaProbe::durationFromAbsolutePath($fullPath);
                    }
                    $unregistered[] = [
                        'file_name' => $file,
                        'file_path' => $filePath,
                        'file_type' => $fileType,
                        'file_size' => $fileSize,
                        'duration_sec' => $durationSec,
                    ];
                }
            }

            $this->json(['status' => 'ok', 'data' => $unregistered]);
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Register scanned media files
     */
    public function register(): void {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $files = $input['files'] ?? [];

            $registered = [];
            foreach ($files as $file) {
                $existing = $this->mediaLibrary->findByPath($file['file_path']);
                if (!$existing) {
                    $durationSec = $file['duration_sec'] ?? null;
                    if ($durationSec === null && in_array(strtoupper((string)$file['file_type']), ['AUDIO', 'VIDEO'], true)) {
                        $durationSec = MediaProbe::durationFromWebPath((string)$file['file_path'], $this->publicDir());
                    }
                    $mediaId = $this->mediaLibrary->create([
                        'file_name' => $file['file_name'],
                        'file_path' => $file['file_path'],
                        'file_type' => $file['file_type'],
                        'file_size' => $file['file_size'],
                        'duration_sec' => $durationSec,
                    ]);
                    $registered[] = ['id' => $mediaId, 'file_name' => $file['file_name'], 'duration_sec' => $durationSec];
                }
            }

            $this->json(['status' => 'ok', 'data' => $registered]);
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}

if (!class_exists('MediaLibraryController', false)) {
    class_alias(\App\Controllers\MediaLibraryController::class, 'MediaLibraryController');
}
