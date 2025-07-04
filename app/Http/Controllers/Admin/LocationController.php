<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LocationController extends Controller
{
    public function index(Request $request)
    {
        $query = Location::withCount(['employees', 'attendances']);

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $locations = $query->paginate(15);

        return view('admin.locations.index', compact('locations'));
    }

    public function create()
    {
        return view('admin.locations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:1|max:10000',
        ]);

        Location::create($request->all());

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location created successfully.');
    }

    public function show(Location $location)
    {
        $location->loadCount(['employees', 'attendances']);
        $recentAttendances = $location->attendances()
            ->with(['employee.user'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.locations.show', compact('location', 'recentAttendances'));
    }

    public function edit(Location $location)
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'address' => 'required|string',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => 'required|integer|min:1|max:10000',
            'status' => 'required|in:active,inactive',
        ]);

        $location->update($request->all());

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        if ($location->employees()->count() > 0) {
            return redirect()->route('admin.locations.index')
                ->with('error', 'Cannot delete location that has assigned employees.');
        }

        if ($location->attendances()->count() > 0) {
            return redirect()->route('admin.locations.index')
                ->with('error', 'Cannot delete location that has attendance records.');
        }

        $location->delete();

        return redirect()->route('admin.locations.index')
            ->with('success', 'Location deleted successfully.');
    }

    public function validateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'location_id' => 'required|exists:locations,id',
        ]);

        $location = Location::find($request->location_id);
        $distance = $this->calculateDistance(
            $request->latitude,
            $request->longitude,
            $location->latitude,
            $location->longitude
        );

        $isWithinRadius = $distance <= $location->radius_meters;

        return response()->json([
            'valid' => $isWithinRadius,
            'distance' => round($distance, 2),
            'radius' => $location->radius_meters,
            'location_name' => $location->name,
        ]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
