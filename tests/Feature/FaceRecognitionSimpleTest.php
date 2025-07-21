<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Employee;
use App\Services\FaceApiService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\Helpers\TestImageHelper;

class FaceRecognitionSimpleTest extends TestCase
{
    use RefreshDatabase;

    protected FaceApiService $faceApiService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->faceApiService = app(FaceApiService::class);
    }

    public function test_face_api_service_can_be_initialized(): void
    {
        $this->assertInstanceOf(FaceApiService::class, $this->faceApiService);
        $this->assertNotEmpty($this->faceApiService->getGalleryId());
    }

    public function test_face_api_service_has_similarity_threshold(): void
    {
        $this->assertTrue($this->faceApiService->isAboveThreshold(0.85));
        $this->assertFalse($this->faceApiService->isAboveThreshold(0.60));
    }

    public function test_face_api_service_can_validate_base64_images(): void
    {
        $validBase64 = TestImageHelper::getValidBase64Image();
        $invalidBase64 = TestImageHelper::getInvalidBase64Image();

        $this->assertTrue($this->faceApiService->validateBase64Image($validBase64));
        $this->assertFalse($this->faceApiService->validateBase64Image($invalidBase64));
    }

    public function test_face_enrollment_api_response(): void
    {
        Http::fake([
            '*/facegallery/enroll-face' => Http::response([
                'risetai' => [
                    'status' => '200',
                    'status_message' => 'Success',
                    'face_id' => 'face_123',
                    'user_id' => 'emp_001'
                ]
            ], 200)
        ]);

        $user = User::factory()->create(['role' => 'employee']);
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $base64Image = TestImageHelper::getValidBase64Image();

        $response = $this->faceApiService->enrollEmployeeFace(
            $employee->employee_id,
            $user->name,
            $base64Image
        );

        $this->assertEquals('200', $response['status']);
        $this->assertEquals('Success', $response['status_message']);
        $this->assertArrayHasKey('face_id', $response);
    }

    public function test_face_verification_api_response(): void
    {
        Http::fake([
            '*/facegallery/verify-face' => Http::response([
                'risetai' => [
                    'status' => '200',
                    'status_message' => 'Success',
                    'verified' => true,
                    'similarity' => 0.95,
                    'user_id' => 'emp_001'
                ]
            ], 200)
        ]);

        $user = User::factory()->create(['role' => 'employee']);
        $employee = Employee::factory()->create(['user_id' => $user->id]);
        $base64Image = TestImageHelper::getValidBase64Image();

        $response = $this->faceApiService->verifyEmployeeFace(
            $employee->employee_id,
            $base64Image
        );

        $this->assertEquals('200', $response['status']);
        $this->assertTrue($response['verified']);
        $this->assertGreaterThan(0.9, $response['similarity']);
    }

    public function test_face_identification_api_response(): void
    {
        Http::fake([
            '*/facegallery/identify-face' => Http::response([
                'risetai' => [
                    'status' => '200',
                    'status_message' => 'Success',
                    'user_id' => 'emp_001',
                    'similarity' => 0.92,
                    'verified' => true
                ]
            ], 200)
        ]);

        $base64Image = TestImageHelper::getValidBase64Image();

        $response = $this->faceApiService->identifyEmployeeFace($base64Image);

        $this->assertEquals('200', $response['status']);
        $this->assertArrayHasKey('user_id', $response);
        $this->assertArrayHasKey('similarity', $response);
    }

    public function test_face_deletion_api_response(): void
    {
        Http::fake([
            '*/facegallery/delete-face' => Http::response([
                'risetai' => [
                    'status' => '200',
                    'status_message' => 'Face deleted successfully'
                ]
            ], 200)
        ]);

        $user = User::factory()->create(['role' => 'employee']);
        $employee = Employee::factory()->create(['user_id' => $user->id]);

        $response = $this->faceApiService->deleteEmployeeFace($employee->employee_id);

        $this->assertEquals('200', $response['status']);
        $this->assertStringContainsString('deleted', $response['status_message']);
    }

    public function test_face_gallery_creation(): void
    {
        Http::fake([
            '*/facegallery/create-facegallery' => Http::response([
                'risetai' => [
                    'status' => '200',
                    'status_message' => 'Gallery created successfully',
                    'facegallery_id' => 'test_gallery'
                ]
            ], 200)
        ]);

        $response = $this->faceApiService->createFaceGallery('test_gallery');

        $this->assertEquals('200', $response['status']);
        $this->assertStringContainsString('created', $response['status_message']);
    }

    public function test_face_gallery_listing(): void
    {
        Http::fake([
            '*/facegallery/my-facegalleries' => Http::response([
                'risetai' => [
                    'status' => '200',
                    'status_message' => 'Success',
                    'facegalleries' => ['gallery_1', 'gallery_2']
                ]
            ], 200)
        ]);

        $response = $this->faceApiService->getMyFaceGalleries();

        $this->assertEquals('200', $response['status']);
        $this->assertArrayHasKey('facegallery_id', $response);
        $this->assertIsArray($response['facegallery_id']);
    }

    public function test_employee_face_enrollment_status(): void
    {
        $employee = Employee::factory()->create();

        // Test the isFaceEnrolled method exists and returns boolean
        $this->assertIsBool($employee->isFaceEnrolled());
    }
}