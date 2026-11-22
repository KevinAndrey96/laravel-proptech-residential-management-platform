<?php

use GuzzleHttp\Client;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Request as RequestAlias;

if (!function_exists('uploadFile')) {
    function uploadFile(UploadedFile $file, int $fileName, string $path): void
    {
        $pathName = sprintf('%s/%s.png', trim($path, '/'), $fileName);
        Storage::disk('public')->put($pathName, file_get_contents($file));

        $url = env('URL_UPLOADER');
        if (!empty($url)) {
            try {
                $client = new Client();
                $localPath = Storage::disk('public')->path($pathName);
                if (file_exists($localPath)) {
                    $client->request(RequestAlias::METHOD_POST, $url, [
                        'multipart' => [
                            [
                                'name' => 'image',
                                'contents' => fopen($localPath, 'r')
                            ],
                            [
                                'name' => 'path',
                                'contents' => $path
                            ]
                        ]
                    ]);
                    @unlink($localPath);
                }
            } catch (\Throwable $e) {
                Log::warning('Remote upload failed, keeping local file: ' . $e->getMessage());
            }
        }
    }
}
