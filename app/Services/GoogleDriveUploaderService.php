<?php

namespace App\Services;

use Carbon\Carbon;
use Google\Client as GoogleClient;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;

class GoogleDriveUploaderService
{
    protected Drive $service;

    public function __construct()
    {
        $client = new GoogleClient([
            'timeout' => 30,
        ]);
        $client->setApplicationName('LaravelUploader');
        $client->useApplicationDefaultCredentials();
        $client->setAuthConfig(base_path('settings/google-credentials.json'));
        $client->addScope([Drive::DRIVE]);
        $client->setAccessType('offline');

        $this->service = new Drive($client);
    }

    public function uploadImage(UploadedFile $file, string $prefix = 'logo'): string
    {
        $folderId = $this->getOrCreateFolder(); // auto-create PPDB/yyyy-mm-dd
        $fileName = time() . '-' . $prefix . '.' . $file->getClientOriginalExtension();

        $fileMetadata = new DriveFile([
            'name' => $fileName,
            'parents' => [$folderId],
        ]);

        $content = file_get_contents($file->getRealPath());

        $uploadedFile = $this->service->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id',
        ]);

        $this->makePublic($uploadedFile->id);
        // Check if the file was uploaded successfully
        if (!$uploadedFile) {
            throw new \Exception('Failed to upload file to Google Drive');
        }
        // Return the public URL for the uploaded file
        return $uploadedFile->id;
    }

    public function getOrCreateFolder(): string
    {
        $rootId = env('GOOGLE_IMAGE_DRIVE_FOLDER_ID', 'NoRootId');
        $mainFolder = env('GOOGLE_IMAGE_DRIVE_FOLDER_NAME', 'PPDB');
        $today = now()->translatedFormat('Y-m-d');

        $path = [$mainFolder, $today];
        $parentId = $rootId;

        foreach ($path as $folderName) {
            $query = "name = '$folderName' and mimeType = 'application/vnd.google-apps.folder' and '$parentId' in parents and trashed = false";

            $folders = $this->service->files->listFiles([
                'q' => $query,
                'spaces' => 'drive',
                'fields' => 'files(id, name)',
            ])->getFiles();

            if (count($folders) > 0) {
                $parentId = $folders[0]->getId();
            } else {
                $folder = new DriveFile([
                    'name' => $folderName,
                    'mimeType' => 'application/vnd.google-apps.folder',
                    'parents' => [$parentId],
                ]);

                $created = $this->service->files->create($folder, ['fields' => 'id']);
                $parentId = $created->getId();
            }
        }

        return $parentId;
    }


    protected function makePublic(string $fileId): void
    {
        $permission = new Drive\Permission([
            'type' => 'anyone',
            'role' => 'reader',
        ]);

        $this->service->permissions->create($fileId, $permission);
    }

    public function deleteFileByUrl(?string $url): void
    {
        if (!$url) return;

        $fileId = $this->extractFileId($url);
        if ($fileId) {
            try {
                $this->service->files->delete($fileId);
            } catch (\Exception $e) {
                // Log jika gagal hapus (misal file sudah tidak ada)
            }
        }
    }

    protected function extractFileId(string $url): ?string
    {
        // Untuk format: https://drive.google.com/uc?id=FILE_ID
        parse_str(parse_url($url, PHP_URL_QUERY), $query);
        return $query['id'] ?? null;
    }
}