<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaUploadController extends Controller
{
    /**
     * Generate a temporary presigned upload URL for direct client-to-R2 streaming.
     * Bypasses PHP and server memory limits for large video files (2GB - 2.5GB).
     */
    public function generateUploadUrl(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'filename' => ['required', 'string', 'max:255'],
            'content_type' => ['nullable', 'string', 'max:100'],
            'folder' => ['nullable', 'string', 'in:movies,episodes,trailers'],
        ]);

        $folder = $validated['folder'] ?? 'movies';
        $contentType = $validated['content_type'] ?: 'video/mp4';

        // Generate clean unique storage key
        $cleanName = Str::slug(pathinfo($validated['filename'], PATHINFO_FILENAME));
        $extension = strtolower(pathinfo($validated['filename'], PATHINFO_EXTENSION)) ?: 'mp4';
        $key = "{$folder}/" . Str::uuid() . "-{$cleanName}.{$extension}";

        $bucket = config('filesystems.disks.r2.bucket', 'vjflix-media');
        $disk = Storage::disk('r2');

        try {
            // Check if disk supports S3 presigned upload URLs (native AwsS3V3Adapter)
            if (method_exists($disk, 'temporaryUploadUrl')) {
                $uploadData = $disk->temporaryUploadUrl($key, now()->addMinutes(60), [
                    'ContentType' => $contentType,
                ]);
                $uploadUrl = $uploadData['url'];
            } elseif (method_exists($disk, 'getClient')) {
                /** @var \Aws\S3\S3Client $s3Client */
                $s3Client = $disk->getClient();
                $command = $s3Client->getCommand('PutObject', [
                    'Bucket' => $bucket,
                    'Key' => $key,
                    'ContentType' => $contentType,
                ]);
                $presignedRequest = $s3Client->createPresignedRequest($command, '+60 minutes');
                $uploadUrl = (string) $presignedRequest->getUri();
            } else {
                // Fallback for fake/local storage in testing
                $uploadUrl = "https://{$bucket}.r2.cloudflarestorage.com/{$key}?mock_token=test";
            }
        } catch (\Throwable $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Cloudflare R2 signing error: ' . $e->getMessage() . '. Please verify CLOUDFLARE_R2_ACCESS_KEY_ID and CLOUDFLARE_R2_SECRET_ACCESS_KEY in your .env.',
            ], 500);
        }

        // Resolve public stream URL
        $baseUrl = config('filesystems.disks.r2.url');
        if (! empty($baseUrl)) {
            $publicUrl = rtrim($baseUrl, '/') . '/' . $key;
        } else {
            $publicUrl = "https://{$bucket}.r2.cloudflarestorage.com/{$key}";
        }

        return response()->json([
            'status' => 'success',
            'upload_url' => $uploadUrl,
            'file_key' => $key,
            'public_url' => $publicUrl,
        ]);
    }
}
