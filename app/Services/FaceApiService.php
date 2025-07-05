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
    protected string $galleryId;

    public function __construct()
    {
        $this->baseUrl = config('services.biznet_face.url');
        $this->accessToken = config('services.biznet_face.token');
        $this->timeout = config('services.biznet_face.timeout', 30);
        $this->similarityThreshold = config('services.biznet_face.similarity_threshold', 0.75);
        $this->galleryId = config('services.biznet_face.gallery_id', 'main_gallery');
    }

    protected function makeRequest(string $endpoint, array $data = [], string $method = 'POST'): Response
    {
        $url = rtrim($this->baseUrl, '/') . '/' . $endpoint;
        
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

    public function getCounters(?string $trxId = null): array
    {
        $data = [
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('client/get-counters', $data, 'GET');
        $result = $response->json() ?? [];
        
        // Handle the risetai wrapper in the response
        if (isset($result['risetai'])) {
            return $result['risetai'];
        }
        
        return $result;
    }

    public function getMyFaceGalleries(): array
    {
        $response = $this->makeRequest('facegallery/my-facegalleries', [], 'GET');
        $result = $response->json() ?? [];
        
        // Handle the risetai wrapper in the response
        if (isset($result['risetai'])) {
            // Transform the response to match expected structure
            return [
                'status' => $result['risetai']['status'] ?? '200',
                'status_message' => $result['risetai']['status_message'] ?? 'Success',
                'facegallery_id' => $result['risetai']['facegalleries'] ?? []
            ];
        }
        
        return $result;
    }

    public function createFaceGallery(string $galleryId, ?string $trxId = null): array
    {
        $data = [
            'facegallery_id' => $galleryId,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/create-facegallery', $data);
        $result = $response->json() ?? [];
        
        // Handle the risetai wrapper in the response
        if (isset($result['risetai'])) {
            return $result['risetai'];
        }
        
        return $result;
    }

    public function deleteFaceGallery(string $galleryId, ?string $trxId = null): array
    {
        $data = [
            'facegallery_id' => $galleryId,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/delete-facegallery', $data, 'DELETE');
        
        $result = $response->json() ?? [];
        
        // Handle the risetai wrapper in the response
        if (isset($result['risetai'])) {
            return $result['risetai'];
        }
        
        return $result;
    }

    public function enrollFace(string $userId, string $userName, string $galleryId, string $base64Image, ?string $trxId = null): array
    {
        $data = [
            'user_id' => $userId,
            'user_name' => $userName,
            'facegallery_id' => $galleryId,
            'image' => $base64Image,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/enroll-face', $data);
        
        $result = $response->json() ?? [];
        
        // Handle the risetai wrapper in the response
        if (isset($result['risetai'])) {
            return $result['risetai'];
        }
        
        return $result;
    }

    public function listFaces(string $galleryId, ?string $trxId = null): array
    {
        $data = [
            'facegallery_id' => $galleryId,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/list-faces', $data, 'GET');
        $result = $response->json() ?? [];
        
        // Handle the risetai wrapper in the response
        if (isset($result['risetai'])) {
            return $result['risetai'];
        }
        
        return $result;
    }

    public function verifyFace(string $userId, string $galleryId, string $base64Image, ?string $trxId = null): array
    {
        $data = [
            'user_id' => $userId,
            'facegallery_id' => $galleryId,
            'image' => $base64Image,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/verify-face', $data);
        
        $result = $response->json() ?? [];
        
        // Handle the risetai wrapper in the response
        if (isset($result['risetai'])) {
            return $result['risetai'];
        }
        
        return $result;
    }

    public function identifyFace(string $galleryId, string $base64Image, ?string $trxId = null): array
    {
        $data = [
            'facegallery_id' => $galleryId,
            'image' => $base64Image,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/identify-face', $data);
        
        $result = $response->json() ?? [];
        
        // Handle the risetai wrapper in the response
        if (isset($result['risetai'])) {
            return $result['risetai'];
        }
        
        return $result;
    }

    public function deleteFace(string $userId, string $galleryId, ?string $trxId = null): array
    {
        $data = [
            'user_id' => $userId,
            'facegallery_id' => $galleryId,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('facegallery/delete-face', $data, 'DELETE');
        
        $result = $response->json() ?? [];
        
        // Handle the risetai wrapper in the response
        if (isset($result['risetai'])) {
            return $result['risetai'];
        }
        
        return $result;
    }

    public function compareImages(string $sourceImage, string $targetImage, ?string $trxId = null): array
    {
        $data = [
            'source_image' => $sourceImage,
            'target_image' => $targetImage,
            'trx_id' => $trxId ?? $this->generateTrxId()
        ];

        $response = $this->makeRequest('compare-images', $data);
        
        $result = $response->json() ?? [];
        
        // Handle the risetai wrapper in the response
        if (isset($result['risetai'])) {
            return $result['risetai'];
        }
        
        return $result;
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

    // Convenience methods using the configured gallery ID
    public function getGalleryId(): string
    {
        return $this->galleryId;
    }

    public function enrollEmployeeFace(string $userId, string $userName, string $base64Image, ?string $trxId = null): array
    {
        return $this->enrollFace($userId, $userName, $this->galleryId, $base64Image, $trxId);
    }

    public function verifyEmployeeFace(string $userId, string $base64Image, ?string $trxId = null): array
    {
        return $this->verifyFace($userId, $this->galleryId, $base64Image, $trxId);
    }

    public function identifyEmployeeFace(string $base64Image, ?string $trxId = null): array
    {
        return $this->identifyFace($this->galleryId, $base64Image, $trxId);
    }

    public function deleteEmployeeFace(string $userId, ?string $trxId = null): array
    {
        return $this->deleteFace($userId, $this->galleryId, $trxId);
    }

    public function listAllFaces(?string $trxId = null): array
    {
        return $this->listFaces($this->galleryId, $trxId);
    }

    public function initializeGallery(): array
    {
        return $this->createFaceGallery($this->galleryId);
    }
}