<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class DriveProxyController extends Controller
{
    public function show($id)
    {
        $url = "https://drive.google.com/uc?export=download&id={$id}";
        $response = Http::withHeaders([
            'User-Agent' => 'Laravel Proxy',
        ])->get($url);

        if (!$response->successful()) {
            return response('Failed to fetch image', 404);
        }
        return new Response(
            $response->body(),
            200,
            [
                'Content-Type' => $response->header('Content-Type', 'image/jpeg'),
                'Cache-Control' => 'public, max-age=86400',
            ]
        );
    }
}