<?php

namespace App\Services;

use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FaceApiService
{
    protected string $baseUrl;
    protected string $accessToken;
    protected int $timeout;
    protected float $similarityThreshold;

    public function __construct()
    {
        $this->baseUrl = config('services.biznet_face.url');
        $this->accessToken = config('services.biznet_face.token');
        $this->timeout = config('services.biznet_face.timeout', 30);
        $this->similarityThreshold = config('services.biznet_face.similarity_threshold', 0.75);
    }

    protected function makeRequest(string $endpoint, array $data = [], string $method = 'POST'): Response
    {
        $url = $this->baseUrl . $endpoint;
        
        $response = Http::timeout($this->timeout)
            ->withHeaders([
                'Accesstoken' => $this->accessToken,
                'Content-Type' => 'application/json',
            ])
            ->send($method, $url, [
                'json' => $data
            ]);

        if ($response->failed()) {
            Log::error('Face API request failed', [
                'endpoint' => $endpoint,
                'status' => $response->status(),
                'response' => $response->body(),
                'data' => $data
            ]);
        }

        return $response;
    }

    public function getCounters(string $trxId = null): array
    {
        $data = [
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('client/get-counters', $data, 'GET');
        
        return $response->json();
    }

    public function getMyFaceGalleries(): array
    {
        $response = $this->makeRequest('facegallery/my-facegalleries', [], 'GET');
        
        return $response->json();
    }

    public function createFaceGallery(string $galleryId, string $trxId = null): array
    {
        $data = [
            'facegallery_id' => $galleryId,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/create-facegallery', $data);
        
        return $response->json();
    }

    public function deleteFaceGallery(string $galleryId, string $trxId = null): array
    {
        $data = [
            'facegallery_id' => $galleryId,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/delete-facegallery', $data, 'DELETE');
        
        return $response->json();
    }

    public function enrollFace(string $userId, string $userName, string $galleryId, string $base64Image, string $trxId = null): array
    {
        $data = [
            'user_id' => $userId,
            'user_name' => $userName,
            'facegallery_id' => $galleryId,
            'image' => $base64Image,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/enroll-face', $data);
        
        return $response->json();
    }

    public function listFaces(string $galleryId, string $trxId = null): array
    {
        $data = [
            'facegallery_id' => $galleryId,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/list-faces', $data, 'GET');
        
        return $response->json();
    }

    public function verifyFace(string $userId, string $galleryId, string $base64Image, string $trxId = null): array
    {
        $data = [
            'user_id' => $userId,
            'facegallery_id' => $galleryId,
            'image' => $base64Image,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/verify-face', $data);
        
        return $response->json();
    }

    public function identifyFace(string $galleryId, string $base64Image, string $trxId = null): array
    {
        $data = [
            'facegallery_id' => $galleryId,
            'image' => $base64Image,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/identify-face', $data);
        
        return $response->json();
    }

    public function deleteFace(string $userId, string $galleryId, string $trxId = null): array
    {
        $data = [
            'user_id' => $userId,
            'facegallery_id' => $galleryId,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/delete-face', $data, 'DELETE');
        
        return $response->json();
    }

    public function compareImages(string $sourceImage, string $targetImage, string $trxId = null): array
    {
        $data = [
            'source_image' => $sourceImage,
            'target_image' => $targetImage,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('compare-images', $data);
        
        return $response->json();
    }

    public function isVerified(array $response): bool
    {
        return isset($response['verified']) && $response['verified'] === true;
    }

    public function getSimilarity(array $response): float
    {
        return $response['similarity'] ?? 0.0;
    }

    public function isAboveThreshold(float $similarity): bool
    {
        return $similarity >= $this->similarityThreshold;
    }

    public function encodeImageToBase64(string $imagePath): string
    {
        if (!file_exists($imagePath)) {
            throw new Exception("Image file not found: {$imagePath}");
        }

        $imageData = file_get_contents($imagePath);
        return base64_encode($imageData);
    }

    public function validateBase64Image(string $base64Image): bool
    {
        $decoded = base64_decode($base64Image, true);
        
        if ($decoded === false) {
            return false;
        }

        $imageInfo = @getimagesizefromstring($decoded);
        
        return $imageInfo !== false;
    }

    protected function generateTrxId(): string
    {
        return 'trx_' . time() . '_' . uniqid();
    }
}