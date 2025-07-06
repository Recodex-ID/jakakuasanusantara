<x-layouts.app>
    <x-slot name="title">Edit Location</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

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
                            <h1 class="text-2xl font-bold text-gray-900">Edit Location</h1>
                            <p class="text-gray-600">Update attendance location information and GPS coordinates</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('admin.locations.show', $location) }}"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-eye class="w-5 h-5 mr-2" />
                                View Details
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

            <!-- Form -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('admin.locations.update', $location) }}" class="space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Basic Information -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <x-forms.input label="Location Name *" name="name" type="text"
                                        placeholder="e.g., Head Office, Branch Office"
                                        value="{{ old('name', $location->name) }}" required />
                                </div>

                                <div class="md:col-span-2">
                                    <x-forms.textarea label="Address *" name="address"
                                        placeholder="Full address of the location..." rows="3"
                                        value="{{ old('address', $location->address) }}" required />
                                </div>

                                <div>
                                    <x-forms.select label="Status *" name="status" :options="['active' => 'Active', 'inactive' => 'Inactive']" :selected="old('status', $location->status)"
                                        required />
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
                                    Click on the map to select coordinates, or drag the marker to reposition
                                </p>
                            </div>

                            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                                <div>
                                    <x-forms.input label="Latitude *" name="latitude" type="number" step="any"
                                        placeholder="e.g., -6.2088" value="{{ old('latitude', $location->latitude) }}"
                                        required />
                                </div>

                                <div>
                                    <x-forms.input label="Longitude *" name="longitude" type="number" step="any"
                                        placeholder="e.g., 106.8456"
                                        value="{{ old('longitude', $location->longitude) }}" required />
                                </div>

                                <div>
                                    <x-forms.input label="Radius (meters) *" name="radius_meters" type="number"
                                        placeholder="e.g., 100"
                                        value="{{ old('radius_meters', $location->radius_meters) }}" min="1"
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
                                How to update GPS coordinates
                            </h4>
                            <ul class="text-sm text-blue-700 space-y-1">
                                <li>• Click anywhere on the map to set new coordinates</li>
                                <li>• Drag the marker to fine-tune the position</li>
                                <li>• Use "Use Current Location" for approximate location (IP-based)</li>
                                <li>• For precise coordinates: use Google Maps, right-click location, copy coordinates
                                </li>
                                <li>• Manually edit the latitude/longitude fields below</li>
                                <li>• Adjust the radius to change the attendance boundary</li>
                            </ul>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-end space-x-3 pt-6 border-t border-gray-200">
                            <a href="{{ route('admin.locations.show', $location) }}"
                                class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors">
                                Cancel
                            </a>
                            <x-button type="primary" buttonType="submit">
                                Update Location
                            </x-button>
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
            const initialLat = parseFloat(document.getElementById('latitude').value) || -6.2088;
            const initialLng = parseFloat(document.getElementById('longitude').value) || 106.8456;
            const initialRadius = parseInt(document.getElementById('radius_meters').value) || 100;

            // Create map centered on the location
            map = L.map('map').setView([initialLat, initialLng], 15);

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
                radius: initialRadius
            }).addTo(map);

            // Add popup to marker
            marker.bindPopup(`
                <div class="text-center">
                    <strong>{{ $location->name }}</strong><br>
                    <small>Drag to reposition</small>
                </div>
            `).openPopup();

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

            // Update popup
            marker.bindPopup(`
                <div class="text-center">
                    <strong>{{ $location->name }}</strong><br>
                    <small>Lat: ${lat.toFixed(4)}, Lng: ${lng.toFixed(4)}</small>
                </div>
            `);

            // Center map on new location if it's far from current view
            const currentCenter = map.getCenter();
            const distance = currentCenter.distanceTo([lat, lng]);
            if (distance > 1000) { // If more than 1km away
                map.setView([lat, lng]);
            }
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
            // Check if geolocation is supported
            if (!navigator.geolocation) {
                showFlashMessage('Geolocation is not supported by this browser.', 'error');
                return;
            }

            // Check if we're on HTTPS (required for geolocation in production)
            if (location.protocol !== 'https:' && location.hostname !== 'localhost' && location.hostname !== '127.0.0.1') {
                showFlashMessage('Geolocation requires HTTPS connection.', 'error');
                return;
            }

            // Show loading state
            const button = document.querySelector('button[onclick="getCurrentLocation()"]');
            const originalText = button.innerHTML;
            button.innerHTML =
                '<span class="animate-spin inline-block w-4 h-4 border-2 border-white border-t-transparent rounded-full mr-2"></span>Getting location...';
            button.disabled = true;

            // Get current position with options - try high accuracy first, then fallback
            const highAccuracyOptions = {
                enableHighAccuracy: true,
                timeout: 15000, // 15 seconds
                maximumAge: 60000 // 1 minute
            };

            const lowAccuracyOptions = {
                enableHighAccuracy: false,
                timeout: 10000, // 10 seconds
                maximumAge: 300000 // 5 minutes
            };

            // Try high accuracy first
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Success callback
                    updateLocation(position.coords.latitude, position.coords.longitude);
                    showFlashMessage('Location coordinates updated successfully!', 'success');

                    // Reset button
                    button.innerHTML = originalText;
                    button.disabled = false;
                },
                function(error) {
                    // High accuracy failed, try low accuracy
                    console.log('High accuracy failed:', error.message);

                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            // Success callback for low accuracy
                            updateLocation(position.coords.latitude, position.coords.longitude);
                            showFlashMessage('Location coordinates updated successfully! (Low accuracy)',
                            'success');

                            // Reset button
                            button.innerHTML = originalText;
                            button.disabled = false;
                        },
                        function(error) {
                            // Both GPS attempts failed, try IP-based geolocation
                            console.log('Both accuracy attempts failed:', error.message);
                            console.log('Trying IP-based geolocation as fallback...');

                            // Try IP-based geolocation as last resort
                            tryIPGeolocation(button, originalText, error);
                        },
                        lowAccuracyOptions
                    );
                },
                highAccuracyOptions
            );
        }

        // IP-based geolocation fallback
        function tryIPGeolocation(button, originalText, originalError) {
            // Try multiple IP geolocation services
            const ipServices = [
                'https://ipapi.co/json/',
                'https://ipinfo.io/json',
                'https://api.ipgeolocation.io/ipgeo?apiKey=&ip='
            ];

            async function tryService(serviceUrl) {
                try {
                    const response = await fetch(serviceUrl);
                    const data = await response.json();

                    let lat, lng;

                    // Handle different API response formats
                    if (data.latitude && data.longitude) {
                        lat = parseFloat(data.latitude);
                        lng = parseFloat(data.longitude);
                    } else if (data.lat && data.lon) {
                        lat = parseFloat(data.lat);
                        lng = parseFloat(data.lon);
                    } else if (data.loc) {
                        const coords = data.loc.split(',');
                        lat = parseFloat(coords[0]);
                        lng = parseFloat(coords[1]);
                    }

                    if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
                        updateLocation(lat, lng);
                        showFlashMessage('Location updated using IP geolocation (approximate)', 'success');
                        button.innerHTML = originalText;
                        button.disabled = false;
                        return true;
                    }

                    return false;
                } catch (error) {
                    console.log('IP geolocation service failed:', error);
                    return false;
                }
            }

            // Try services one by one
            (async () => {
                for (const service of ipServices) {
                    if (await tryService(service)) {
                        return; // Success, exit
                    }
                }

                // All services failed, show original error
                let errorMessage = 'Unable to get your location.';
                let suggestions = '';

                switch (originalError.code) {
                    case originalError.PERMISSION_DENIED:
                        errorMessage = 'Location access denied.';
                        suggestions = 'Please enable location permission in your browser settings and try again.';
                        break;
                    case originalError.POSITION_UNAVAILABLE:
                        errorMessage = 'GPS location unavailable.';
                        suggestions =
                            'Try enabling location services in System Preferences → Security & Privacy → Location Services, or enter coordinates manually.';
                        break;
                    case originalError.TIMEOUT:
                        errorMessage = 'Location request timed out.';
                        suggestions = 'Please try again or enter coordinates manually.';
                        break;
                    default:
                        errorMessage = 'Location detection failed.';
                        suggestions = 'Please enter coordinates manually using the map or input fields.';
                }

                showFlashMessage(errorMessage + ' ' + suggestions, 'error');

                // Reset button
                button.innerHTML = originalText;
                button.disabled = false;
            })();
        }

        // Show flash message using the exact same structure as flash-messages component
        function showFlashMessage(message, type = 'success') {
            const flashContainer = document.getElementById('flash-container') || createFlashContainer();

            // Create the exact same HTML structure as the Blade component
            const flashDiv = document.createElement('div');
            flashDiv.className = 'flash-message px-4 py-3 rounded-lg shadow-lg max-w-sm';
            flashDiv.setAttribute('role', 'alert');

            if (type === 'success') {
                flashDiv.className += ' bg-green-100 border border-green-400 text-green-700';
                flashDiv.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">${message}</span>
                        <button type="button" class="ml-3 -mx-1.5 -my-1.5 text-green-500 hover:text-green-600 rounded-lg focus:ring-2 focus:ring-green-300 p-1.5 hover:bg-green-200 inline-flex items-center justify-center h-8 w-8" onclick="this.parentElement.parentElement.remove()">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                `;
            } else {
                flashDiv.className += ' bg-red-100 border border-red-400 text-red-700';
                flashDiv.innerHTML = `
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="font-medium">${message}</span>
                        <button type="button" class="ml-3 -mx-1.5 -my-1.5 text-red-500 hover:text-red-600 rounded-lg focus:ring-2 focus:ring-red-300 p-1.5 hover:bg-red-200 inline-flex items-center justify-center h-8 w-8" onclick="this.parentElement.parentElement.remove()">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                `;
            }

            flashContainer.appendChild(flashDiv);

            // Auto remove after 5 seconds (same as the component)
            setTimeout(() => {
                if (flashDiv.parentElement) {
                    flashDiv.style.opacity = '0';
                    flashDiv.style.transform = 'translateX(100%)';
                    flashDiv.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    setTimeout(() => {
                        if (flashDiv.parentElement) {
                            flashDiv.remove();
                        }
                    }, 300);
                }
            }, 5000);
        }

        // Create flash container if it doesn't exist
        function createFlashContainer() {
            const container = document.createElement('div');
            container.id = 'flash-container';
            container.className = 'fixed top-4 right-4 z-[9999] space-y-2';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
            return container;
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
