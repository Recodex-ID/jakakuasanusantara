<x-layouts.app>
    <x-slot name="title">Face Registration</x-slot>

    <!-- Flash Messages -->
    <x-flash-messages />

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Face Registration</h1>
                            <p class="text-gray-600">Register your face for attendance tracking system</p>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500">{{ now()->format('l, F j, Y') }}</div>
                            <div class="text-lg font-semibold text-gray-900">{{ now()->format('H:i') }}</div>
                        </div>
                    </div>
                </div>
            </div>

            @if($faceEnrolled)
                <!-- Already Enrolled -->
                <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded-lg shadow-sm mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <x-fas-check class="h-5 w-5 text-green-400" />
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-green-800">
                                Face Already Registered
                            </h3>
                            <div class="mt-2 text-sm text-green-700">
                                <p>Your face has been successfully registered in the system. You can now use face recognition for attendance tracking.</p>
                            </div>
                            <div class="mt-4">
                                <div class="flex space-x-2">
                                    <a href="{{ route('employee.attendance.index') }}" class="bg-green-100 hover:bg-green-200 text-green-800 px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                        Record Attendance
                                    </a>
                                    <button onclick="confirmDelete()" class="bg-white hover:bg-gray-50 text-green-800 px-3 py-2 rounded-md text-sm font-medium border border-green-200 transition-colors">
                                        Re-register Face
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Registration Form -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Camera Section -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Face Capture</h3>

                            <div class="space-y-4">
                                <!-- Camera View -->
                                <div class="relative">
                                    <video id="video" class="w-full h-64 bg-gray-200 rounded-lg object-cover" autoplay playsinline></video>
                                    <canvas id="canvas" class="hidden"></canvas>

                                    <!-- Camera Status Overlay -->
                                    <div id="cameraStatus" class="absolute top-2 right-2 px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                        Camera Off
                                    </div>

                                    <!-- Face Detection Overlay -->
                                    <div id="faceDetection" class="absolute inset-0 pointer-events-none hidden">
                                        <div class="w-full h-full border-2 border-green-400 rounded-lg animate-pulse"></div>
                                        <div class="absolute top-2 left-2 px-2 py-1 bg-green-100 text-green-800 text-xs font-medium rounded">
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

                    <!-- Instructions & Registration -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Registration Instructions</h3>

                            <!-- Instructions -->
                            <div class="space-y-4 mb-6">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold text-blue-600">1</span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        <strong>Position yourself properly:</strong> Face the camera directly with good lighting.
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold text-blue-600">2</span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        <strong>Remove accessories:</strong> Take off glasses, hats, or masks if possible.
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold text-blue-600">3</span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        <strong>Stay still:</strong> Look directly at the camera and remain still during capture.
                                    </div>
                                </div>
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-xs font-bold text-blue-600">4</span>
                                        </div>
                                    </div>
                                    <div class="text-sm text-gray-700">
                                        <strong>Click register:</strong> When ready, click the registration button below.
                                    </div>
                                </div>
                            </div>

                            <!-- Registration Form -->
                            <form id="enrollmentForm" class="space-y-4">
                                @csrf
                                
                                <!-- Employee Info -->
                                <div class="p-4 bg-gray-50 rounded-lg">
                                    <div class="text-sm text-gray-600">
                                        <div><strong>Name:</strong> {{ $employee->user->name }}</div>
                                        <div><strong>Employee ID:</strong> {{ $employee->employee_id }}</div>
                                        <div><strong>Department:</strong> {{ $employee->department ?? 'Not assigned' }}</div>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <button type="submit" id="submitBtn" disabled
                                    class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 disabled:bg-gray-300 text-white font-semibold rounded-lg transition-colors">
                                    <x-fas-camera class="w-5 h-5 inline mr-2" />
                                    Register Face
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

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
                        <a href="{{ route('employee.attendance.index') }}"
                            class="flex items-center justify-center px-4 py-3 bg-green-50 hover:bg-green-100 text-green-700 font-medium rounded-lg transition-colors">
                            <x-fas-clock class="w-5 h-5 mr-2" />
                            Record Attendance
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
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                    <x-fas-spinner class="animate-spin h-6 w-6 text-green-600" />
                </div>
                <h3 class="text-lg font-medium text-gray-900 mt-2">Registering Face</h3>
                <p class="text-sm text-gray-500 mt-1">Please wait while we register your face...</p>
            </div>
        </div>
    </div>

    <script>
        let stream = null;

        // Initialize when page loads
        document.addEventListener('DOMContentLoaded', function() {
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

        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({
                    video: {
                        width: { ideal: 640 },
                        height: { ideal: 480 },
                        facingMode: 'user'
                    }
                });

                document.getElementById('video').srcObject = stream;
                document.getElementById('cameraStatus').className = 'absolute top-2 right-2 px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800';
                document.getElementById('cameraStatus').textContent = 'Camera Active';

                document.getElementById('startCamera').disabled = true;
                document.getElementById('stopCamera').disabled = false;

                updateSubmitButton();

                // Start basic face detection simulation
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
                document.getElementById('cameraStatus').className = 'absolute top-2 right-2 px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800';
                document.getElementById('cameraStatus').textContent = 'Camera Off';
                document.getElementById('faceDetection').classList.add('hidden');

                document.getElementById('startCamera').disabled = false;
                document.getElementById('stopCamera').disabled = true;

                updateSubmitButton();
            }
        }

        function updateSubmitButton() {
            const submitBtn = document.getElementById('submitBtn');
            const hasCamera = stream !== null;

            if (submitBtn) {
                submitBtn.disabled = !hasCamera;

                if (!hasCamera) {
                    submitBtn.textContent = 'Start camera first';
                } else {
                    submitBtn.innerHTML = `
                        <x-fas-camera class="w-5 h-5 inline mr-2" />
                        Register Face
                    `;
                }
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

        function confirmDelete() {
            if (confirm('Are you sure you want to delete your current face registration and register a new one?')) {
                deleteFaceData();
            }
        }

        async function deleteFaceData() {
            try {
                const response = await fetch('{{ route('employee.face-enrollment.destroy') }}', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    alert('Face data deleted successfully! You can now register a new face.');
                    window.location.reload();
                } else {
                    alert('Failed to delete face data: ' + data.message);
                }
            } catch (error) {
                alert('An error occurred while deleting face data');
                console.error('Delete error:', error);
            }
        }

        if (document.getElementById('enrollmentForm')) {
            document.getElementById('enrollmentForm').addEventListener('submit', async function(e) {
                e.preventDefault();

                document.getElementById('loadingModal').classList.remove('hidden');

                try {
                    const faceImage = await capturePhoto();
                    const formData = new FormData(this);

                    formData.append('face_image', faceImage);

                    const response = await fetch('{{ route('employee.face-enrollment.store') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: formData
                    });

                    const data = await response.json();

                    if (data.success) {
                        alert('Face registered successfully! You can now use face recognition for attendance.');
                        window.location.reload();
                    } else {
                        alert('Failed to register face: ' + data.message);
                    }

                } catch (error) {
                    alert('An error occurred while registering face');
                    console.error('Enrollment error:', error);
                } finally {
                    document.getElementById('loadingModal').classList.add('hidden');
                }
            });
        }

        // Cleanup on page unload
        window.addEventListener('beforeunload', function() {
            if (stream) {
                stopCamera();
            }
        });
    </script>
</x-layouts.app>