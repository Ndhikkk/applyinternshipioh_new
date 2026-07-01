<?php

namespace App\Libraries;

use CodeIgniter\Files\File;

class SupabaseStorage
{
    private $url;
    private $key;
    private $bucket;

    public function __construct()
    {
        $this->url    = getenv('SUPABASE_URL');
        $this->key    = getenv('SUPABASE_KEY');
        $this->bucket = getenv('SUPABASE_BUCKET');
    }

    /**
     * Upload a file to Supabase Storage
     *
     * @param File   $file     The file object from CodeIgniter request
     * @param string $folder   Target folder in bucket (e.g., 'cv', 'ktm')
     * @param string $fileName Target filename (optional, default to original)
     *
     * @return string|false Public URL of the uploaded file or false on failure
     */
    public function upload($file, $folder, $fileName = null)
    {
        if (!$this->url || !$this->key || !$this->bucket) {
            log_message('error', 'Supabase configuration missing.');
            return false;
        }

        if (!$fileName) {
            $fileName = $file->getName();
        }

        // Clean filename to be URL safe
        $fileName = preg_replace('/[^a-zA-Z0-9.\-_]/', '', $fileName);
        $targetPath = $folder . '/' . time() . '_' . $fileName;

        $endpoint = rtrim($this->url, '/') . '/storage/v1/object/' . $this->bucket . '/' . $targetPath;

        $fileContent = file_get_contents($file->getTempName());
        $mimeType = $file->getMimeType();

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fileContent);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . $this->key,
            'Content-Type: ' . $mimeType,
            'x-upsert: true' // Overwrite if exists
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            // Success. Return public public URL.
            return rtrim($this->url, '/') . '/storage/v1/object/public/' . $this->bucket . '/' . $targetPath;
        } else {
            log_message('error', 'Supabase Upload Failed: ' . $response);
            return false;
        }
    }
}
