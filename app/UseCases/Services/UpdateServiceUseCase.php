<?php

namespace App\UseCases\Services;

use App\Models\Service;
use App\UseCases\Contracts\Services\UpdateServiceUseCaseInterface;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Request as RequestAlias;

class UpdateServiceUseCase implements UpdateServiceUseCaseInterface
{
    public function handle(Request $request): ?Service
    {
        $service = Service::find($request->input('service_id'));
        if (!$service) {
            return null;
        }

        $service->title = (string) $request->input('title');
        $service->description = (string) $request->input('description');
        $service->capacity = (int) $request->input('capacity');
        $service->strip = (int) $request->input('strip');
        $service->start = $request->input('start');
        $service->final = $request->input('final');
        $service->state = $request->input('state') === 'enable' || (bool) $request->input('state');

        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($days as $day) {
            $val = $request->input($day);
            $service->{$day} = ($val === true || $val === 'true' || $val === 1 || $val === '1');
        }

        $service->save();

        if ($request->has('gallery') && is_iterable($request->gallery)) {
            $service->gallery = '';
            $url = env('URL_UPLOADER');
            foreach ($request->gallery as $i => $file) {
                $slugTitle = str_replace(' ', '-', strtolower($service->title)) . '-' . $i;
                $pathName = 'services/' . $slugTitle . '.png';
                Storage::disk('public')->put($pathName, file_get_contents($file));
                $service->gallery .= '/storage/' . $pathName . ';';

                if (!empty($url)) {
                    try {
                        $localPath = Storage::disk('public')->path($pathName);
                        if (file_exists($localPath)) {
                            $client = new Client();
                            $client->request(RequestAlias::METHOD_POST, $url, [
                                'multipart' => [
                                    ['name' => 'image', 'contents' => fopen($localPath, 'r')],
                                    ['name' => 'path', 'contents' => 'services']
                                ]
                            ]);
                            @unlink($localPath);
                        }
                    } catch (\Throwable $e) {
                        // Keep local storage copy
                    }
                }
            }
            $service->save();
        }

        return $service;
    }
}
