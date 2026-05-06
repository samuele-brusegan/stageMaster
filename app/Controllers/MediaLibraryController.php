<?php

require_once BASE_PATH . '/app/Controllers/ApiController.php';

class MediaLibraryController extends ApiController {

    private $db;
    private $mediaLibrary;

    public function __construct() {
        $this->db = (new DatabaseConnector())->getConnection();
        $this->mediaLibrary = new \App\Models\MediaLibrary($this->db);
    }

    /**
     * Get all media
     */
    public function index() {
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
    public function upload() {
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
            $durationSec = $this->getMediaDuration($uploadPath);
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

    /**
     * Get media duration using ffprobe
     */
    private function getMediaDuration($filePath) {
        $output = shell_exec("ffprobe -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 " . escapeshellarg($filePath) . " 2>&1");
        if ($output && is_numeric($output)) {
            return (int) $output;
        }
        return null;
    }

    /**
     * Delete media
     */
    public function delete() {
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
    public function scan() {
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

                    $unregistered[] = [
                        'file_name' => $file,
                        'file_path' => $filePath,
                        'file_type' => $fileType,
                        'file_size' => $fileSize
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
    public function register() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $files = $input['files'] ?? [];

            $registered = [];
            foreach ($files as $file) {
                $existing = $this->mediaLibrary->findByPath($file['file_path']);
                if (!$existing) {
                    $mediaId = $this->mediaLibrary->create([
                        'file_name' => $file['file_name'],
                        'file_path' => $file['file_path'],
                        'file_type' => $file['file_type'],
                        'file_size' => $file['file_size'],
                        'duration_sec' => null
                    ]);
                    $registered[] = ['id' => $mediaId, 'file_name' => $file['file_name']];
                }
            }

            $this->json(['status' => 'ok', 'data' => $registered]);
        } catch (\Exception $e) {
            $this->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
