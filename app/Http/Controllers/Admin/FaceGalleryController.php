<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaceGallery;
use App\Models\Location;
use App\Services\FaceApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class FaceGalleryController extends Controller
{
    protected FaceApiService $faceApiService;

    public function __construct(FaceApiService $faceApiService)
    {
        $this->faceApiService = $faceApiService;
    }

    public function index(Request $request)
    {
        $query = FaceGallery::with('location');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('gallery_id', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('location_id')) {
            $query->where('location_id', $request->get('location_id'));
        }

        $galleries = $query->paginate(15);
        $locations = Location::where('status', 'active')->get();

        return view('admin.face-galleries.index', compact('galleries', 'locations'));
    }

    public function create()
    {
        $locations = Location::where('status', 'active')->get();
        return view('admin.face-galleries.create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'gallery_id' => 'required|string|unique:face_galleries|regex:/^[a-zA-Z0-9._@-]+$/',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location_id' => 'nullable|exists:locations,id',
        ]);

        try {
            $response = $this->faceApiService->createFaceGallery($request->gallery_id);
            
            if ($response['status'] === '200') {
                FaceGallery::create($request->all());
                
                return redirect()->route('admin.face-galleries.index')
                    ->with('success', 'Face Gallery created successfully.');
            } else {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'Failed to create gallery in Face API: ' . ($response['status_message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Face Gallery creation failed', [
                'gallery_id' => $request->gallery_id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->back()
                ->withInput()
                ->with('error', 'An error occurred while creating the face gallery.');
        }
    }

    public function show(FaceGallery $faceGallery)
    {
        $faceGallery->load('location');
        
        try {
            $facesResponse = $this->faceApiService->listFaces($faceGallery->gallery_id);
            $enrolledFaces = $facesResponse['faces'] ?? [];
        } catch (\Exception $e) {
            $enrolledFaces = [];
            Log::warning('Failed to fetch faces from API', [
                'gallery_id' => $faceGallery->gallery_id,
                'error' => $e->getMessage()
            ]);
        }

        return view('admin.face-galleries.show', compact('faceGallery', 'enrolledFaces'));
    }

    public function edit(FaceGallery $faceGallery)
    {
        $locations = Location::where('status', 'active')->get();
        return view('admin.face-galleries.edit', compact('faceGallery', 'locations'));
    }

    public function update(Request $request, FaceGallery $faceGallery)
    {
        $request->validate([
            'gallery_id' => [
                'required',
                'string',
                'regex:/^[a-zA-Z0-9._@-]+$/',
                Rule::unique('face_galleries')->ignore($faceGallery->id)
            ],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'location_id' => 'nullable|exists:locations,id',
            'status' => 'required|in:active,inactive',
        ]);

        if ($request->gallery_id !== $faceGallery->gallery_id) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Gallery ID cannot be changed after creation.');
        }

        $faceGallery->update($request->all());

        return redirect()->route('admin.face-galleries.index')
            ->with('success', 'Face Gallery updated successfully.');
    }

    public function destroy(FaceGallery $faceGallery)
    {
        try {
            $response = $this->faceApiService->deleteFaceGallery($faceGallery->gallery_id);
            
            if ($response['status'] === '200' || $response['status'] === '416') {
                $faceGallery->delete();
                
                return redirect()->route('admin.face-galleries.index')
                    ->with('success', 'Face Gallery deleted successfully.');
            } else {
                return redirect()->route('admin.face-galleries.index')
                    ->with('error', 'Failed to delete gallery from Face API: ' . ($response['status_message'] ?? 'Unknown error'));
            }
        } catch (\Exception $e) {
            Log::error('Face Gallery deletion failed', [
                'gallery_id' => $faceGallery->gallery_id,
                'error' => $e->getMessage()
            ]);
            
            return redirect()->route('admin.face-galleries.index')
                ->with('error', 'An error occurred while deleting the face gallery.');
        }
    }

    public function syncWithApi()
    {
        try {
            $response = $this->faceApiService->getMyFaceGalleries();
            
            if ($response['status'] === '200') {
                $apiGalleries = $response['facegallery_id'] ?? [];
                $localGalleries = FaceGallery::pluck('gallery_id')->toArray();
                
                $onlyInApi = array_diff($apiGalleries, $localGalleries);
                $onlyLocal = array_diff($localGalleries, $apiGalleries);
                
                return response()->json([
                    'success' => true,
                    'api_galleries' => $apiGalleries,
                    'local_galleries' => $localGalleries,
                    'only_in_api' => $onlyInApi,
                    'only_local' => $onlyLocal,
                    'sync_needed' => !empty($onlyInApi) || !empty($onlyLocal)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch galleries from API'
                ], 422);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while syncing with API',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
