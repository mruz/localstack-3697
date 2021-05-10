<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

Route::middleware('api')->group(function () {
    Route::get('sandbox', function () {
        $settings = [
            'credentials' => [
                'key' => config('filesystems.disks.s3.key'),
                'secret' => config('filesystems.disks.s3.secret'),
            ],
            'region' => config('filesystems.disks.s3.region'),
            'version' => 'latest',
            'endpoint' => config('filesystems.disks.s3.endpoint'),
            'use_path_style_endpoint' => true,
        ];
        $client = S3Client::factory($settings);

        try {
            $client->headBucket([
                'Bucket' => config('filesystems.disks.s3.bucket'),
            ]);
        } catch (S3Exception $e) {
            $client->createBucket([
                'Bucket' => config('filesystems.disks.s3.bucket'),
            ]);
        }

        $disk = Storage::disk('s3');
        $disk->put('signature/test.txt', 'Contents');
        $ttl = now()->addMinutes(5);

        $url = $disk->temporaryUrl('signature/test.txt', $ttl);

        return response()->json($url, 200, [], JSON_UNESCAPED_SLASHES);
    });
});

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
