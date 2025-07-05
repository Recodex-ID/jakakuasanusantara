<x-layouts.app>
    <x-slot name="title">Record Attendance</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Record Attendance</h1>
                            <p class="text-gray-600">Use face recognition to check in or check out</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ now()->format('H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Today's Status -->
            @if ($todayAttendance)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Today's Status</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="text-sm font-medium text-gray-500 mb-2">Check In</div>
                                @if ($todayAttendance->check_in)
                                    <div class="text-2xl font-bold text-green-600">
                                        {{ $todayAttendance->check_in->format('H:i') }}</div>
                                    <div class="text-sm text-gray-500">{{ $todayAttendance->location->name }}</div>
                                @else
                                    <div class="text-2xl font-bold text-gray-400">--:--</div>
                                    <div class="text-sm text-gray-400">Not checked in</div>
                                @endif
                            </div>

                            <div class="text-center">
                                <div class="text-sm font-medium text-gray-500 mb-2">Check Out</div>
                                @if ($todayAttendance->check_out)
                                    <div class="text-2xl font-bold text-red-600">
                                        {{ $todayAttendance->check_out->format('H:i') }}</div>
                                    <div class="text-sm text-gray-500">{{ $todayAttendance->location->name }}</div>
                                @else
                                    <div class="text-2xl font-bold text-gray-400">--:--</div>
                                    <div class="text-sm text-gray-400">Not checked out</div>
                                @endif
                            </div>

                            <div class="text-center">
                                <div class="text-sm font-medium text-gray-500 mb-2">Status</div>
                                <span
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                             @if ($todayAttendance->status === 'present') bg-green-100 text-green-800
                                             @elseif($todayAttendance->status === 'late') bg-yellow-100 text-yellow-800
                                             @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($todayAttendance->status) }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Camera Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Face Recognition Camera</h3>

                        <div class="space-y-4">
                            <!-- Camera View -->
                            <div class="relative">
                                <video id="video" class="w-full h-64 bg-gray-200 rounded-lg object-cover" autoplay
                                    playsinline></video>
                                <canvas id="canvas" class="hidden"></canvas>

                                <!-- Camera Status Overlay -->
                                <div id="cameraStatus"
                                    class="absolute top-2 right-2 px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Camera Off
                                </div>

                                <!-- Face Detection Overlay -->
                                <div id="faceDetection" class="absolute inset-0 pointer-events-none hidden">
                                    <div class="w-full h-full border-2 border-green-400 rounded-lg animate-pulse"></div>
                                    <div
                                        class="absolute top-2 left-2 px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">
                                        Face Detected
                                    </div>
                                </div>
                            </div>

                            <!-- Camera Controls -->
                            <div class="flex justify-center space-x-3">
                                <button id="startCamera" onclick="startCamera()"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                                    <x-fas-video class="w-5 h-5 inline mr-2" />
                                    Start Camera
                                </button>
                                <button id="stopCamera" onclick="stopCamera()" disabled
                                    class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white font-medium rounded-lg transition-colors disabled:opacity-50">
                                    <x-fas-stop class="w-5 h-5 inline mr-2" />
                                    Stop Camera
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Actions -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Attendance Actions</h3>

                        <form id="attendanceForm" class="space-y-4">
                            @csrf

                            <!-- Location Selection -->
                            <div>
                                <label for="location_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Location <span class="text-red-500">*</span>
                                </label>
                                <select name="location_id" id="location_id" required
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">Choose a location...</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}" data-lat="{{ $location->latitude }}"
                                            data-lng="{{ $location->longitude }}"
                                            data-radius="{{ $location->radius_meters }}">
                                            {{ $location->name }} - {{ $location->address }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Action Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Action <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label
                                        class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-green-300 transition-colors">
                                        <input type="radio" name="action" value="check_in" required
                                            class="text-green-600 focus:ring-green-500"
                                            {{ !$todayAttendance || !$todayAttendance->check_in ? 'checked' : 'disabled' }}>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">Check In</div>
                                            <div class="text-xs text-gray-500">Start your workday</div>
                                        </div>
                                    </label>

                                    <label
                                        class="flex items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-red-300 transition-colors">
                                        <input type="radio" name="action" value="check_out" required
                                            class="text-red-600 focus:ring-red-500"
                                            {{ $todayAttendance && $todayAttendance->check_in && !$todayAttendance->check_out ? '' : 'disabled' }}>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">Check Out</div>
                                            <div class="text-xs text-gray-500">End your workday</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- GPS Status -->
                            <div class="p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-700">GPS Location</span>
                                    <div id="gpsStatus" class="flex items-center text-sm">
                                        <div class="w-2 h-2 bg-gray-400 rounded-full mr-2"></div>
                                        <span class="text-gray-500">Not detected</span>
                                    </div>
                                </div>
                                <div id="locationInfo" class="mt-2 text-xs text-gray-500 hidden">
                                    <div>Coordinates: <span id="coordinates">-</span></div>
                                    <div>Distance from location: <span id="distance">-</span></div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <button type="submit" id="submitBtn" disabled
                                class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-300 text-white font-semibold rounded-lg transition-colors">
                                <x-fas-camera class="w-5 h-5 inline mr-2" />
                                Take Photo & Record Attendance
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <a href="{{ route('employee.dashboard') }}"
                            class="flex items-center justify-center px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 font-medium rounded-lg transition-colors">
                            <x-fas-tachometer-alt class="w-5 h-5 mr-2" />
                            Dashboard
                        </a>
                        <a href="{{ route('employee.attendance.history') }}"
                            class="flex items-center justify-center px-4 py-3 bg-green-50 hover:bg-green-100 text-green-700 font-medium rounded-lg transition-colors">
                            <x-fas-clipboard-list class="w-5 h-5 mr-2" />
                            View History
                        </a>
                        <a href="{{ route('settings.profile.edit') }}"
                            class="flex items-center justify-center px-4 py-3 bg-purple-50 hover:bg-purple-100 text-purple-700 font-medium rounded-lg transition-colors">
                            <x-fas-user class="w-5 h-5 mr-2" />
                            Profile Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loadingModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                    <x-fas-spinner class="animate-spin h-6 w-6 text-blue-600" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-2">Processing Attendance</h3>
                <p class="text-sm text-gray-500 mt-1" id="loadingStatus">Please wait while we verify your face and
                    record attendance...</p>
            </div>
        </div>
    </div>

    <script>
        let stream = null;
        let userLocation = null;
        let selectedLocation = null;

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
            getCurrentLocation();
            updateTime();
            setInterval(updateTime, 1000);
        });

        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', {
                hour12: false,
                hour: '2-digit',
                minute: '2-digit'
            });

            const timeDisplay = document.querySelector('.text-lg.font-semibold.text-gray-900');
            if (timeDisplay) {
                timeDisplay.textContent = timeString;
            }
        }

        function getCurrentLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        userLocation = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude
                        };

                        updateGPSStatus(true);
                        updateLocationDistance();
                    },
                    function(error) {
                        updateGPSStatus(false, error.message);
                    }, {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 300000
                    }
                );
            } else {
                updateGPSStatus(false, 'Geolocation not supported');
            }
        }

        function updateGPSStatus(success, error = null) {
            const status = document.getElementById('gpsStatus');
            const info = document.getElementById('locationInfo');
            const coords = document.getElementById('coordinates');

            if (success) {
                status.innerHTML = `
                    <div class="w-2 h-2 bg-green-400 rounded-full mr-2"></div>
                    <span class="text-green-600">Location detected</span>
                `;
                coords.textContent = `${userLocation.lat.toFixed(6)}, ${userLocation.lng.toFixed(6)}`;
                info.classList.remove('hidden');
            } else {
                status.innerHTML = `
                    <div class="w-2 h-2 bg-red-400 rounded-full mr-2"></div>
                    <span class="text-red-600">Location error</span>
                `;
                if (error) {
                    console.error('GPS Error:', error);
                }
            }

            updateSubmitButton();
        }

        function updateLocationDistance() {
            const locationSelect = document.getElementById('location_id');
            const selectedOption = locationSelect.options[locationSelect.selectedIndex];
            const distanceEl = document.getElementById('distance');

            if (selectedOption && selectedOption.value && userLocation) {
                const locationLat = parseFloat(selectedOption.dataset.lat);
                const locationLng = parseFloat(selectedOption.dataset.lng);
                const radius = parseInt(selectedOption.dataset.radius);

                const distance = calculateDistance(
                    userLocation.lat, userLocation.lng,
                    locationLat, locationLng
                );

                selectedLocation = {
                    lat: locationLat,
                    lng: locationLng,
                    radius: radius,
                    distance: distance
                };

                distanceEl.textContent = `${distance.toFixed(0)}m (max: ${radius}m)`;
                distanceEl.className = distance <= radius ? 'text-green-600' : 'text-red-600';
            } else {
                distanceEl.textContent = '-';
                distanceEl.className = '';
                selectedLocation = null;
            }

            updateSubmitButton();
        }

        function calculateDistance(lat1, lon1, lat2, lon2) {
            const R = 6371000; // Earth's radius in meters
            const dLat = (lat2 - lat1) * Math.PI / 180;
            const dLon = (lon2 - lon1) * Math.PI / 180;
            const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                Math.sin(dLon / 2) * Math.sin(dLon / 2);
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
            return R * c;
        }

        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            const locationSelect = document.getElementById('location_id');
            const actionInputs = document.querySelectorAll('input[name="action"]');

            const hasLocation = locationSelect.value !== '';
            const hasAction = Array.from(actionInputs).some(input => input.checked && !input.disabled);
            const hasGPS = userLocation !== null;
            const hasCamera = stream !== null;
            const inRange = selectedLocation ? selectedLocation.distance <= selectedLocation.radius : false;

            const canSubmit = hasLocation && hasAction && hasGPS && hasCamera && inRange;

            submitBtn.disabled = !canSubmit;

            if (!canSubmit) {
                let reason = 'Requirements: ';
                const missing = [];
                if (!hasLocation) missing.push('location');
                if (!hasAction) missing.push('action');
                if (!hasGPS) missing.push('GPS');
                if (!hasCamera) missing.push('camera');
                if (selectedLocation && !inRange) missing.push('within range');

                submitBtn.textContent = reason + missing.join(', ');
            } else {
                submitBtn.innerHTML = `
                    <x-fas-camera class="w-5 h-5 inline mr-2" />
                    Take Photo & Record Attendance
                `;
            }
        }

        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: {
                            ideal: 640
                        },
                        height: {
                            ideal: 480
                        },
                        facingMode: 'user'
                    }
                });

                document.getElementById('video').srcObject = stream;
                document.getElementById('cameraStatus').className =
                    'absolute top-2 right-2 px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800';
                document.getElementById('cameraStatus').textContent = 'Camera Active';

                document.getElementById('startCamera').disabled = true;
                document.getElementById('stopCamera').disabled = false;

                updateSubmitButton();

                // Start basic face detection simulation (in real app, use proper face detection)
                setTimeout(() => {
                    document.getElementById('faceDetection').classList.remove('hidden');
                }, 2000);

            } catch (error) {
                alert('Camera access denied or not available: ' + error.message);
                console.error('Camera error:', error);
            }
        }

        function stopCamera() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                stream = null;

                document.getElementById('video').srcObject = null;
                document.getElementById('cameraStatus').className =
                    'absolute top-2 right-2 px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800';
                document.getElementById('cameraStatus').textContent = 'Camera Off';
                document.getElementById('faceDetection').classList.add('hidden');

                document.getElementById('startCamera').disabled = false;
                document.getElementById('stopCamera').disabled = true;

                updateSubmitButton();
            }
        }

        function capturePhoto() {
            const video = document.getElementById('video');
            const canvas = document.getElementById('canvas');
            const context = canvas.getContext('2d');

            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0);

            return new Promise((resolve) => {
                canvas.toBlob(function(blob) {
                    const reader = new FileReader();
                    reader.onloadend = function() {
                        resolve(reader.result.split(',')[1]);
                    };
                    reader.readAsDataURL(blob);
                }, 'image/jpeg', 0.8);
            });
        }

        document.getElementById('location_id').addEventListener('change', updateLocationDistance);

        document.getElementById('attendanceForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            document.getElementById('loadingModal').classList.remove('hidden');

            try {
                const faceImage = await capturePhoto();
                const formData = new FormData(this);

                formData.append('face_image', faceImage);
                formData.append('latitude', userLocation.lat);
                formData.append('longitude', userLocation.lng);

                const response = await fetch('{{ route('employee.attendance.record') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    alert('Attendance recorded successfully!');
                    window.location.reload();
                } else {
                    alert('Failed to record attendance: ' + data.message);
                }

            } catch (error) {
                alert('An error occurred while recording attendance');
                console.error('Attendance error:', error);
            } finally {
                document.getElementById('loadingModal').classList.add('hidden');
            }
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (stream) {
                stopCamera();
            }
        });
    </script>
</x-layouts.app>
