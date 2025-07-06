<x-layouts.app>
    <x-slot name="title">Location Details</x-slot>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <div class="py-6">
        <div class="container mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $location->name }}</h1>
                            <p class="text-gray-600">Location details and attendance information</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.locations.edit', $location) }}"
                                class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-edit class="w-5 h-5 mr-2" />
                                Edit Location
                            </a>
                            <a href="{{ route('admin.locations.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-arrow-left class="w-5 h-5 mr-2" />
                                Back to Locations
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Location Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Location Map -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Map</h3>
                            <div id="map" class="w-full h-80 rounded-lg border border-gray-300"></div>
                        </div>
                    </div>

                    <!-- Basic Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Location Name</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $location->name }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1
                                                 {{ $location->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ ucfirst($location->status) }}
                                    </span>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700">Address</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $location->address }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- GPS Coordinates -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">GPS Coordinates</h3>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Latitude</label>
                                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $location->latitude }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Longitude</label>
                                    <p class="mt-1 text-sm text-gray-900 font-mono">{{ $location->longitude }}</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Radius</label>
                                    <p class="mt-1 text-sm text-gray-900">{{ $location->radius_meters }} meters</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Attendances -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recent Attendances</h3>
                            @if ($recentAttendances->isEmpty())
                                <div class="text-center py-8">
                                    <x-fas-clock class="w-12 h-12 mx-auto mb-4 text-gray-400" />
                                    <p class="text-gray-500">No recent attendance records.</p>
                                </div>
                            @else
                                <div class="space-y-3">
                                    @foreach ($recentAttendances as $attendance)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                            <div class="flex items-center space-x-3">
                                                <div
                                                    class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center">
                                                    <span class="text-xs font-medium text-blue-900">
                                                        {{ $attendance->employee->user->initials() }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h4 class="font-medium text-gray-900">
                                                        {{ $attendance->employee->user->name }}</h4>
                                                    <p class="text-sm text-gray-500">
                                                        {{ $attendance->employee->employee_id }}</p>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm font-medium text-gray-900">
                                                    {{ $attendance->date->format('M j, Y') }}</p>
                                                <div class="flex space-x-2 text-xs">
                                                    @if ($attendance->check_in)
                                                        <span class="text-green-600">In:
                                                            {{ $attendance->check_in->format('H:i') }}</span>
                                                    @endif
                                                    @if ($attendance->check_out)
                                                        <span class="text-red-600">Out:
                                                            {{ $attendance->check_out->format('H:i') }}</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Statistics & Actions -->
                <div class="space-y-6">
                    <!-- Statistics -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Statistics</h3>
                            <div class="space-y-4">
                                <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                                    <div class="flex items-center">
                                        <x-fas-users class="w-5 h-5 text-blue-600 mr-3" />
                                        <span class="text-sm font-medium text-gray-700">Assigned Employees</span>
                                    </div>
                                    <span
                                        class="text-lg font-bold text-blue-600">{{ $location->employees_count }}</span>
                                </div>
                                <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                                    <div class="flex items-center">
                                        <x-fas-clock class="w-5 h-5 text-green-600 mr-3" />
                                        <span class="text-sm font-medium text-gray-700">Total Attendances</span>
                                    </div>
                                    <span
                                        class="text-lg font-bold text-green-600">{{ $location->attendances_count }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                            <div class="space-y-3">
                                <a href="{{ route('admin.locations.edit', $location) }}"
                                    class="w-full flex items-center justify-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white font-medium rounded-lg transition-colors">
                                    <x-fas-edit class="w-5 h-5 mr-2" />
                                    Edit Location
                                </a>
                                <x-button type="primary" onclick="centerMap()" class="w-full">
                                    <x-fas-map class="w-5 h-5 mr-2" />
                                    Center Map
                                </x-button>
                                @if ($location->employees_count == 0 && $location->attendances_count == 0)
                                    <form method="POST" action="{{ route('admin.locations.destroy', $location) }}"
                                        onsubmit="return confirmDelete()">
                                        @csrf
                                        @method('DELETE')
                                        <x-button type="danger" buttonType="submit" class="w-full">
                                            <x-fas-trash class="w-5 h-5 mr-2" />
                                            Delete Location
                                        </x-button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Location Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Location Info</h3>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center">
                                    <x-fas-map-marker-alt class="w-4 h-4 text-gray-400 mr-3" />
                                    <span class="text-gray-600">{{ $location->latitude }},
                                        {{ $location->longitude }}</span>
                                </div>
                                <div class="flex items-center">
                                    <x-fas-circle class="w-4 h-4 text-gray-400 mr-3" />
                                    <span class="text-gray-600">{{ $location->radius_meters }}m radius</span>
                                </div>
                                <div class="flex items-center">
                                    <x-fas-calendar class="w-4 h-4 text-gray-400 mr-3" />
                                    <span class="text-gray-600">Created
                                        {{ $location->created_at->format('M j, Y') }}</span>
                                </div>
                                <div class="flex items-center">
                                    <x-fas-edit class="w-4 h-4 text-gray-400 mr-3" />
                                    <span class="text-gray-600">Updated
                                        {{ $location->updated_at->format('M j, Y') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        let map;
        let marker;
        let radiusCircle;

        // Initialize map
        function initMap() {
            const lat = {{ $location->latitude }};
            const lng = {{ $location->longitude }};
            const radius = {{ $location->radius_meters }};

            // Create map
            map = L.map('map').setView([lat, lng], 16);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add marker
            marker = L.marker([lat, lng]).addTo(map);

            // Add radius circle
            radiusCircle = L.circle([lat, lng], {
                color: 'blue',
                fillColor: '#3084ff',
                fillOpacity: 0.2,
                radius: radius
            }).addTo(map);

            // Add popup
            marker.bindPopup(`
                <div class="text-center">
                    <strong>{{ $location->name }}</strong><br>
                    <small>{{ $location->address }}</small><br>
                    <small>Radius: ${radius}m</small>
                </div>
            `).openPopup();

            // Fit map to show marker and radius
            const group = new L.featureGroup([marker, radiusCircle]);
            map.fitBounds(group.getBounds().pad(0.1));
        }

        // Center map on location
        function centerMap() {
            const lat = {{ $location->latitude }};
            const lng = {{ $location->longitude }};
            map.setView([lat, lng], 16);
        }

        // Confirm delete
        function confirmDelete() {
            return confirm('Are you sure you want to delete this location? This action cannot be undone.');
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
        });
    </script>
</x-layouts.app>
