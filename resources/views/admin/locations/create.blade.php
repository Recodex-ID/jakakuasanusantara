<x-layouts.app>
    <x-slot name="title">Create Location</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Create New Location</h1>
                            <p class="text-gray-600">Add a new attendance location with GPS coordinates</p>
                        </div>
                        <a href="{{ route('admin.locations.index') }}"
                            class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors">
                            <x-fas-arrow-left class="w-5 h-5 mr-2" />
                            Back to Locations
                        </a>
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.locations.store') }}" class="space-y-6">
                        @csrf

                        <!-- Basic Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-forms.input label="Location Name *" name="name" type="text"
                                        placeholder="e.g., Head Office, Branch Office" value="{{ old('name') }}"
                                        required />
                                </div>
                                
                                <div class="md:col-span-2">
                                    <x-forms.textarea label="Address *" name="address"
                                        placeholder="Full address of the location..." rows="3" required>{{ old('address') }}</x-forms.textarea>
                                </div>
                            </div>
                        </div>

                        <!-- GPS Coordinates -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">GPS Coordinates</h3>

                            <!-- Interactive Map -->
                            <div class="mb-6">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Location on Map
                                </label>
                                <div id="map" class="w-full h-96 rounded-lg border border-gray-300"></div>
                                <p class="text-sm text-gray-500 mt-2">
                                    <x-fas-info-circle class="w-4 h-4 mr-1 inline" />
                                    Click on the map to select coordinates, or use the buttons below to get your current
                                    location
                                </p>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                <div>
                                    <x-forms.input label="Latitude *" name="latitude" type="number" step="any"
                                        placeholder="e.g., -6.2088" value="{{ old('latitude', -6.2088) }}" required />
                                </div>

                                <div>
                                    <x-forms.input label="Longitude *" name="longitude" type="number" step="any"
                                        placeholder="e.g., 106.8456" value="{{ old('longitude', 106.8456) }}"
                                        required />
                                </div>

                                <div>
                                    <x-forms.input label="Radius (meters) *" name="radius_meters" type="number"
                                        placeholder="e.g., 100" value="{{ old('radius_meters', 100) }}" min="1"
                                        max="10000" required />
                                </div>

                                <div class="flex items-end">
                                    <button type="button" onclick="getCurrentLocation()"
                                        class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                                        <x-fas-map-marker-alt class="w-4 h-4 mr-2 inline" />
                                        Use Current Location
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Location Helper -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <h4 class="font-medium text-blue-800 mb-2">
                                <x-fas-info-circle class="w-4 h-4 mr-1 inline" />
                                How to get GPS coordinates
                            </h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Click on the interactive map above to select coordinates</li>
                                <li>• Drag the marker to fine-tune the position</li>
                                <li>• Use "Use Current Location" button for GPS positioning</li>
                                <li>• Manually edit latitude/longitude fields if needed</li>
                            </ul>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.locations.index') }}"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                                Cancel
                            </a>
                            <button type="submit"
                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                Create Location
                            </button>
                        </div>
                    </form>
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
            const initialLat = document.getElementById('latitude').value || -6.2088;
            const initialLng = document.getElementById('longitude').value || 106.8456;
            const initialRadius = document.getElementById('radius_meters').value || 100;

            // Create map centered on Jakarta by default
            map = L.map('map').setView([initialLat, initialLng], 13);

            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // Add marker
            marker = L.marker([initialLat, initialLng], {
                draggable: true
            }).addTo(map);

            // Add radius circle
            radiusCircle = L.circle([initialLat, initialLng], {
                color: 'blue',
                fillColor: '#3084ff',
                fillOpacity: 0.2,
                radius: parseInt(initialRadius)
            }).addTo(map);

            // Handle map click
            map.on('click', function(e) {
                updateLocation(e.latlng.lat, e.latlng.lng);
            });

            // Handle marker drag
            marker.on('dragend', function(e) {
                const position = e.target.getLatLng();
                updateLocation(position.lat, position.lng);
            });
        }

        // Update location coordinates and visual elements
        function updateLocation(lat, lng) {
            // Update form inputs
            document.getElementById('latitude').value = lat.toFixed(6);
            document.getElementById('longitude').value = lng.toFixed(6);

            // Update marker position
            marker.setLatLng([lat, lng]);

            // Update radius circle
            const radius = parseInt(document.getElementById('radius_meters').value) || 100;
            radiusCircle.setLatLng([lat, lng]);
            radiusCircle.setRadius(radius);

            // Center map on new location
            map.setView([lat, lng]);
        }

        // Update radius circle when radius input changes
        function updateRadius() {
            const radius = parseInt(document.getElementById('radius_meters').value) || 100;
            if (radiusCircle) {
                radiusCircle.setRadius(radius);
            }
        }

        // Get current location using geolocation
        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    updateLocation(position.coords.latitude, position.coords.longitude);

                    // Show success message
                    showNotification('Location coordinates updated!', 'success');
                }, function(error) {
                    let errorMessage = 'Unable to get your location.';
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            errorMessage = 'Location access denied by user.';
                            break;
                        case error.POSITION_UNAVAILABLE:
                            errorMessage = 'Location information unavailable.';
                            break;
                        case error.TIMEOUT:
                            errorMessage = 'Location request timed out.';
                            break;
                    }

                    showNotification(errorMessage, 'error');
                });
            } else {
                showNotification('Geolocation is not supported by this browser.', 'error');
            }
        }

        // Show notification
        function showNotification(message, type = 'success') {
            const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
                'bg-red-100 border-red-400 text-red-700';
            const icon = type === 'success' ? '✓' : '✗';

            const successDiv = document.createElement('div');
            successDiv.className = `fixed top-4 right-4 ${bgColor} px-4 py-3 rounded-lg shadow-lg z-50 border`;
            successDiv.innerHTML = `
                <div class="flex items-center">
                    <span class="w-5 h-5 mr-2">${icon}</span>
                    <span>${message}</span>
                </div>
            `;
            document.body.appendChild(successDiv);

            // Auto remove notification
            setTimeout(() => {
                successDiv.remove();
            }, 3000);
        }

        // Update coordinates when inputs change
        function updateMapFromInputs() {
            const lat = parseFloat(document.getElementById('latitude').value);
            const lng = parseFloat(document.getElementById('longitude').value);

            if (!isNaN(lat) && !isNaN(lng)) {
                if (lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
                    updateLocation(lat, lng);
                }
            }
        }

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            initMap();

            // Add event listeners
            document.getElementById('latitude').addEventListener('input', function(e) {
                const value = parseFloat(e.target.value);
                if (value < -90 || value > 90) {
                    e.target.setCustomValidity('Latitude must be between -90 and 90');
                } else {
                    e.target.setCustomValidity('');
                    updateMapFromInputs();
                }
            });

            document.getElementById('longitude').addEventListener('input', function(e) {
                const value = parseFloat(e.target.value);
                if (value < -180 || value > 180) {
                    e.target.setCustomValidity('Longitude must be between -180 and 180');
                } else {
                    e.target.setCustomValidity('');
                    updateMapFromInputs();
                }
            });

            document.getElementById('radius_meters').addEventListener('input', function(e) {
                const value = parseInt(e.target.value);
                if (value < 1 || value > 10000) {
                    e.target.setCustomValidity('Radius must be between 1 and 10000 meters');
                } else {
                    e.target.setCustomValidity('');
                    updateRadius();
                }
            });
        });
    </script>
</x-layouts.app>
