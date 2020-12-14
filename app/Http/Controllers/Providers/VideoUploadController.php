<?php

namespace App\Http\Controllers\Providers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VideoUploadController extends Controller
{
    public function uploadVideos(Request $request) {
        $response = [];
        $responseCode = 200;

        return response($response, $responseCode)
        ->header('Content-Type', 'application/json');
    }
}
